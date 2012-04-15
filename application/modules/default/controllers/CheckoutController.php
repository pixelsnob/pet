<?php

class CheckoutController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_users_svc = new Service_Users;
        $this->_messenger = Zend_Registry::get('messenger');
        $this->view->headLink()->appendStylesheet('/css/cart.css');
    }

    /**
     * 
     */
    public function indexAction() {
        $cart = $this->_cart_svc->get();
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
        /*if ($cart->hasRenewal() && !$this->_users_svc->isAuthenticated()) {
            $this->_forward('login', 'profile', 'default', 
                array('redirect_to' => 'checkout'));
        }*/
        $this->view->cart = $cart;
        $this->view->inlineScriptMin()->loadGroup('checkout')
            ->appendScript('new Pet.CheckoutView;');
    }


}
