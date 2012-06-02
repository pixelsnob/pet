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
        $products_mapper = new Model_Mapper_Products;
        $expiration = new DateTime('2012-09-02');
        //$date->add(new DateInterval('P2D'));
        $subs = $ops_mapper->getByExpiration($expiration);
        foreach ($subs as $sub) {
            if (!$sub->product->is_recurring) {
                continue;
            }
            $min_expiration = new DateTime($sub->min_expiration);
            // Stop repeating around a year -- reference transactions will only
            // last that long...
            if ($expiration->diff($min_expiration)->m > 11) {
                continue; 
            }
            // get order
            // get first order_payment
            // find out what type it is
            // get pnref or correlationid of first payment
            // charge paypal or payflow
            // ++ if charge fails, send email
            // store order_payment data
            // update expiration
            // log
            print_r($sub);
        }
        //$orders = $this->_orders->getByExpiration( 
    }
}
