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
    public function sendOrderEmails() {
        $mail = new Zend_Mail;
        $mail->setBodyText($text_message)
            ->setBodyHtml($html_message)
            ->addTo($cart->billing->email)
            ->setSubject('Photoshop Elements User Order: ' . $cart->order_id)
            ->addBcc($this->_config->store->order_email_bcc->toArray());
        $mail->send();
    }
}
