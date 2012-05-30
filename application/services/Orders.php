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
    }

    /**
     * @return void
     * 
     */
    public function sendOrderEmails() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $orders_mapper = new Model_Mapper_Orders;
        $op_mapper = new Model_Mapper_OrderedProducts;
        $logger = Zend_Registry::get('log');
        try {
            $db->beginTransaction();
            $orders = $orders_mapper->getByEmailSent(false, true);
            $orders_sent = array();
            $mail_exceptions = array();
            foreach ($orders as $order) {
                $ordered_products = $op_mapper->getByOrderId($order->id, true);
                try {
                    $mail = new Zend_Mail;
                    $mail->setBodyText('test')
                         ->setBodyHtml('test')
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
                    $orders_mapper->updateEmailSent($order->id, true);
                }
            }
            $db->commit();
        } catch (Exception $e1) {
            $db->rollback();
            $logger->log('Error updating database while sending order emails',
                Zend_Log::EMERG);
        }
        if (!empty($mail_exceptions)) {
            $logger->log('Error(s) sending order emails: ' .
                count($mail_exceptions) . ' failures', Zend_Log::EMERG);
        }
    }
}
