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
    public function getById($id) {
        return $this->_orders->get($id); 
    }
    
    /**
     * @param int $id
     * @return Model_Order
     * 
     */
    public function getFullOrder($id) {
        $op_mapper          = new Model_Mapper_OrderProducts;
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway_mapper     = new Model_Mapper_PaymentGateway;
        $users_mapper       = new Model_Mapper_Users;
        // Get order
        $order = $this->getById($id);
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
        $order->user = $user;
        // Get order products
        $products = $op_mapper->getByOrderId($order->id);
        $temp_products = array();
        if ($products) {
            foreach ($products as $product) {
                $temp_products[] = $products_mapper->getById(
                    $product->product_id); 
            }
            $order->products = $temp_products;
        }
        // Get payment(s)
        $payments = $payments_mapper->getByOrderId($order->id); 
        if (!$payments) {
            $msg = 'Error retrieving from order_payments';
            throw new Exception($msg);
        }
        $order->payments = $payments;
        // Get subscriptions...

        return $order;
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
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payflow_mapper     = new Model_Mapper_OrderPayments_Payflow; 
        $paypal_mapper      = new Model_Mapper_OrderPayments_Paypal; 
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway_mapper     = new Model_Mapper_PaymentGateway;
        $users_mapper       = new Model_Mapper_Users;
        $view               = Zend_Registry::get('view');
        $gateway_exceptions = array();
        $email_exceptions   = array();
        //$expiration      = new DateTime('2012-11-02');
        //$date->add(new DateInterval('P2D'));
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $db->beginTransaction();
            $subs = $ops_mapper->getByExpiration($expiration, true);
            foreach ($subs as $sub) {
                if (!$sub->product->is_recurring) {
                    continue;
                }
                $status = true;
                $log_data = array();
                $min_expiration = new DateTime($sub->min_expiration);
                // Stop repeating around a year -- reference transactions will only
                // last that long...
                if ($expiration->diff($min_expiration)->m > 11) {
                    continue; 
                }
                // Get order
                $order = $this->getFullOrder($sub->order_id);
                if (!$order) {
                    $msg = 'Error retrieving order';
                    throw new Exception($msg);
                }
                $log_data['order'] = $order;
                $payment = $order->payments[0];
                if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                    // Payment type was payflow
                    $origid = $payment->pnref;
                    $tender = 'C';
                } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                    // Payment type was paypal
                    $origid = $payment->pnref;
                    $tender = 'P';
                }
                // Make a charge attempt
                try {
                    $gateway_mapper->processReferenceTransaction(
                        $sub->product->cost, $origid, $tender); 
                } catch (Exception $e2) {
                    $status = false;
                    $log_data['gateway_exceptions'][0] = $e2->getMessage() . ' ' .
                        $e2->getTraceAsString();
                }
                $log_data['gateway_calls'] = $gateway_mapper->getRawCalls();
                //throw new Exception('shit');
                try {
                    if ($status) {
                        $message = $view->render('emails/recurring_billing_success.phtml');
                    } else {
                        $message = $view->render('emails/recurring_billing_fail.phtml');
                    }
                    $mail = new Zend_Mail;
                    $mail->setBodyText($message)
                         ->addTo($order->user->email)
                         ->setSubject('Customer invoice')
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                } catch (Exception $e3) {
                    // log
                    $log_data['email_exceptions'][0] = $e3->getMessage() .
                        ' ' . $e3->getTraceAsString();
                }
                //print_r($log_data); 
                //print_r($gateway_mapper->getRawCalls());
                // store order_payment data
                // update expiration
                // log
                //print_r($sub);
            } 
            $db->commit();
        } catch (Exception $e) {
            // log !!!!
            // void any transactions
            $gateway_mapper->voidCalls();

            print_r($gateway_mapper->getRawCalls());
        }
    }
}
