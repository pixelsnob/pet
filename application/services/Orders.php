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
     * @return void
     * 
     */
    public function processRecurringBilling() {
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $payments_mapper = new Model_Mapper_OrderPayments;
        $products_mapper = new Model_Mapper_Products;
        $gateway_mapper = new Model_Mapper_PaymentGateway;
        $expiration = new DateTime('2012-11-02');
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
                // Get first payment
                $payments = $payments_mapper->getByOrderId($sub->order_id); 
                if (!isset($payments[0])) {
                    $msg = 'Error retrieving from order_payments';
                    throw new Exception($msg);
                }
                $payment = $payments[0];
                if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                    // Payment type is payflow
                    $opp_mapper = new Model_Mapper_OrderPayments_Payflow; 
                    $payment = $opp_mapper->getByOrderPaymentId($payment->id);
                    if (!$payment) {
                        $msg = 'Error retrieving from order_payments_payflow';
                        throw new Exception($msg);
                    }
                    // charge payflow
                    try {
                        $gateway_mapper->processReferenceTransaction($payment->pnref); 
                    } catch (Exception $e2) {
                        // log/email
                        $status = false;
                        $exceptions[] = $e2;
                    }
                } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                    // Payment type is paypal
                    // charge paypal
                }
                print_r($exceptions);
                print_r($gateway_mapper->getSuccessfulResponseObjects());
                // ++ if charge fails, send email
                // store order_payment data
                // update expiration
                // log
                print_r($sub);
            } 
            $db->commit();
            // send emails
        } catch (Exception $e) {
            // log 
        }
    }
}
