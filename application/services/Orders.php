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
        $mail->setBodyText('test')
            ->setBodyHtml('test')
            ->addTo('snob@pixelsnob.com')
            ->setSubject('Photoshop Elements User Order');
            //->addBcc('');
        $mail->send();
    }
}
