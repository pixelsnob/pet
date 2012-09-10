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
        $this->_orders_mapper = new Model_Mapper_Orders;
    }
    
    /**
     * @param int $order_id
     * @param bool $debug
     * @return void
     * 
     */
    public function sendOrderEmail($order_id, $debug = false) {
        $view = Zend_Registry::get('view');
        $app_config = Zend_Registry::get('app_config');
        $order = $this->_orders_mapper->getFullOrder($order_id);
        $show_shipping = false;
        $has_subscription = false;
        $expiration = null;
        foreach ($order->products as $product) {
            if ($product->isGift()) {
                continue;
            }
            if ($product->isSubscription() || $product->isPhysical()) {
                $show_shipping = true;
            }
            if ($product->isSubscription()) {
                $has_subscription = true;
                $expiration = $order->user->expiration;
            }
        }
        $message = $view->partial('emails/order.phtml', array(
            'expiration'       => $expiration,
            'show_shipping'    => $show_shipping,
            'has_subscription' => $has_subscription,
            'order'            => $order
        ));
        if (!$debug) {
            $mail = new Zend_Mail;
            $mail->setBodyText($message)
                 ->addTo($order->email)
                 ->setSubject('Photoshop Elements User Order: ' . $order->id);
            if ($app_config['order_emails']['bcc']) {
                $mail->addBcc($app_config['order_emails']['bcc']);
            }
            $mail->send();
        } else {
            echo $message . "\n\n";
        }
        if ($order->gifts) {
            foreach ($order->gifts as $gift) {
                $gift_message = $view->partial('emails/gift.phtml', array(
                    'gift'     => $gift,
                    'order'    => $order,
                    'base_url' => $app_config['base_url']
                ));
                if (!$debug) {
                    $mail = new Zend_Mail;
                    $mail->setBodyText($gift_message)
                         ->addTo($order->email)
                         ->setSubject('Your Photoshop Elements Techniques Gift Code is here!');
                    if ($app_config['order_emails']['bcc']) {
                        $mail->addBcc($app_config['order_emails']['bcc']);
                    }
                    $mail->send();
                } else {
                    echo $gift_message . "\n\n";
                }
            }
        }
    }

    /**
     * @param int $order_id
     * @param float $credit_amount
     * @param bool $debug
     * @return void
     * 
     */
    public function sendOrderCreditEmail($order_id, $credit_amount, $debug = false) {
        $view = Zend_Registry::get('view');
        $app_config = Zend_Registry::get('app_config');
        $order = $this->_orders_mapper->getFullOrder($order_id);
        $message = $view->partial('emails/order_credit.phtml', array(
            'order'         => $order,
            'credit_amount' => $credit_amount
        ));
        if (!$debug) {
            $mail = new Zend_Mail;
            $mail->setBodyText($message)
                 ->addTo($order->email)
                 ->setSubject('Photoshop Elements Refund, Order: ' . $order->id);
            if ($app_config['order_emails']['bcc']) {
                $mail->addBcc($app_config['order_emails']['bcc']);
            }
            $mail->send();
        } else {
            echo $message . "\n\n";
        }
    }
}
