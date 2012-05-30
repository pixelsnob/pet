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
        $db = Zend_Db_Table::getDefaultAdapter();
        $logger = Zend_Registry::get('log');
        try {
            $db->beginTransaction();
            $orders = $this->_orders->getByEmailSent(false, true);
            $orders_sent = array();
            $mail_exceptions = array();
            foreach ($orders as $order) {
                try {
                    $mail = new Zend_Mail;
                    $mail->setBodyText('test')
                        ->setBodyHtml('test')
                        ->addTo($order->email)
                        ->setSubject('Photoshop Elements User Order: ' . $order->id)
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
                    throw new Exception('fuck');
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
