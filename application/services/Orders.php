<?php
/**
 * Orders service layer
 *
 * @package Service_Orders
 * 
 */
class Service_Orders {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_orders = new Model_Mapper_Orders;
    }
    
    /**
     * @param int $id
     * @return null|Model_Order
     * 
     */
    public function get($id) {
        return $this->_orders->get($id); 
    }

    /**
     * @return void
     * 
     */
    public function sendOrderEmails() {
        $db            = Zend_Db_Table::getDefaultAdapter();
        $op_mapper     = new Model_Mapper_OrderProducts;
        $logger        = Zend_Registry::get('log');
        $view          = Zend_Registry::get('view');
        try {
            $db->beginTransaction();
            $orders          = $this->_orders->getByEmailSent(false, true);
            $orders_sent     = array();
            $mail_exceptions = array();
            foreach ($orders as $order) {
                $order_products = $op_mapper->getByOrderId($order->id, true);
                try {
                    $mail = new Zend_Mail;
                    $mail->setBodyText($view->render('emails/order.phtml'))
                         ->addTo($order->email)
                         ->setSubject('Photoshop Elements User Order: ' .
                                      $order->id)
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                    $orders_sent[] = $order->id;
                } catch (Exception $e2) {
                    $mail_exceptions[] = $e2;
                }
            }
            if (!empty($orders_sent)) {
                foreach ($orders_sent as $order_id) {
                    $this->_orders->updateEmailSent($order->id, true);
                }
            }
            $db->commit();
        } catch (Exception $e1) {
            $db->rollback();
            $logger->log('Error updating database while sending order emails',
                Zend_Log::EMERG);
        }
        if (!empty($mail_exceptions)) {
            $mail_exceptions_str = implode(' ', $mail_exceptions);
            $logger->log("Error(s) sending order emails: " .
                $mail_exceptions_str, Zend_Log::EMERG);
        }
    }
    
    /**
     * This should be run once per day
     * 
     * @param DateTime $expiration The expiration date to use for the search
     * @return void
     * 
     */
    public function processRecurringBilling(DateTime $expiration) {
        $ops_mapper      = new Model_Mapper_OrderProductSubscriptions;
        $payflow_mapper  = new Model_Mapper_OrderPayments_Payflow; 
        $paypal_mapper   = new Model_Mapper_OrderPayments_Paypal; 
        $payments_mapper = new Model_Mapper_OrderPayments;
        $products_mapper = new Model_Mapper_Products;
        $gateway_mapper  = new Model_Mapper_PaymentGateway;
        $users_mapper    = new Model_Mapper_Users;
        $view            = Zend_Registry::get('view');
        //$expiration      = new DateTime('2012-11-02');
        //$date->add(new DateInterval('P2D'));
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $db->beginTransaction();
            $subs = $ops_mapper->getByExpiration($expiration, true);
            foreach ($subs as $sub) {
                $status = true;
                $exceptions = array();
                if (!$sub->product->is_recurring) {
                    continue;
                }
                $min_expiration = new DateTime($sub->min_expiration);
                // Stop repeating around a year -- reference transactions will only
                // last that long...
                if ($expiration->diff($min_expiration)->m > 11) {
                    continue; 
                }
                // Get order
                $order = $this->get($sub->order_id);
                if (!$order) {
                    $msg = 'Error retrieving order';
                    throw new Exception($msg);
                }
                $user = $users_mapper->getById($order->user_id);
                // Get user
                if (!$user) {
                    $msg = 'Error retrieving user';
                    throw new Exception($msg);
                }
                // Get first payment
                $payments = $payments_mapper->getByOrderId($order->id); 
                if (!isset($payments[0])) {
                    $msg = 'Error retrieving from order_payments';
                    throw new Exception($msg);
                }
                $payment = $payments[0];
                if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                    // Payment type was payflow
                    $payment = $payflow_mapper->getByOrderPaymentId($payment->id);
                    if (!$payment) {
                        $msg = 'Error retrieving from order_payments_payflow';
                        throw new Exception($msg);
                    }
                    $origid = $payment->pnref;
                    $tender = 'C';
                } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                    // Payment type was paypal
                    $payment = $paypal_mapper->getByOrderPaymentId($payment->id);
                    if (!$payment) {
                        $msg = 'Error retrieving from order_payments_paypal';
                        throw new Exception($msg);
                    }
                    $origid = $payment->pnref;
                    $tender = 'P';
                }
                // Make a charge attempt
                try {
                    $gateway_mapper->processReferenceTransaction(
                        $sub->product->cost, $origid, $tender); 
                } catch (Exception $e2) {
                    // log/email
                    //print_r($e2);
                    $status = false;
                    $exceptions[] = $e2;
                }
                if ($status) {
                    $message = $view->render('emails/recurring_billing_success.phtml');
                } else {
                    $message = $view->render('emails/recurring_billing_fail.phtml');
                }
                $mail = new Zend_Mail;
                try {
                    $mail->setBodyText($message)
                         ->addTo($user->email)
                         ->setSubject('Customer invoice')
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                } catch (Exception $e) {
                    // log
                }
                //print_r($gateway_mapper->getRawCalls());
                // store order_payment data
                // update expiration
                // log
                //print_r($sub);
            } 
            $db->commit();
            // send emails
        } catch (Exception $e) {
            // log 
            // void any transactions
            $gateway_mapper->voidCalls();
        }
    }
}
