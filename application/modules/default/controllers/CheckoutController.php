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
        $cart = $this->_cart_svc->get(true);
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
        if ($cart->hasRenewal() && !$this->_users_svc->isAuthenticated()) {
            $msg = 'You must log in to purchase a renewal';
            $this->_messenger->setNamespace('login')->addMessage($msg);
            $this->_forward('login', 'profile', 'default', 
                array('redirect_to' => 'checkout'));
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
            $this->_cart_svc->saveCheckoutForm($post);
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            if ($checkout_form->isValid($post)) {
                if ($this->_cart_svc->process()) {
                    $this->_helper->Redirector->gotoSimple('confirmation');
                } else {
                    $msg = 'There was a problem with your order. Please check ' . 
                           'your information and try again.';
                    $this->_messenger->addMessage($msg);
                }
                return;
            } else {
                $this->_messenger->addMessage('Submitted information is not valid');
            }
        } else {
            $checkout_form = $this->_cart_svc->getCheckoutForm();
        }
        $this->view->cart = $this->_cart_svc->get();
        $this->view->checkout_form = $checkout_form;
        $this->view->cart_totals = $this->_cart_svc->get()->getTotals();
        $this->view->inlineScriptMin()->loadGroup('checkout')
            ->appendScript('new Pet.CheckoutView;');
    }

    public function confirmationAction() {
        //exit('success');
        $confirmation = $this->_cart_svc->getConfirmation();
        if (!$confirmation) {
            $this->_helper->Redirector->gotoSimple('index');
            return;
        }
        print_r($confirmation);
        $this->view->cart = $confirmation->cart;
    }

}
