<?php

class CheckoutController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_users_svc = new Service_Users;
        $this->_messenger = Zend_Registry::get('messenger');
        $this->_messenger->setNamespace('checkout');
    }

    /**
     * 
     */
    public function indexAction() {
        $cart = $this->_cart_svc->get();
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
        if ($cart->hasRenewal() && !$this->_users_svc->isAuthenticated()) {
            $msg = 'You must log in to purchase a renewal';
            $this->_messenger->setNamespace('login')->addMessage($msg);
            $this->_forward('login', 'profile', 'default', 
                array('redirect_to' => 'checkout'));
        }
        $this->view->cart = $cart;
        $checkout_form = $this->_cart_svc->getCheckoutForm();
        $this->view->checkout_form = $checkout_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
           if ($checkout_form->isValid($post)) {
                // redirect here
           } else {
                $this->_messenger->addMessage('Submitted information is not valid');
           }
           $this->_cart_svc->saveCheckoutForm($post);
        }
        $this->view->inlineScriptMin()->loadGroup('checkout')
            ->appendScript('new Pet.CheckoutView;');
    }


}
