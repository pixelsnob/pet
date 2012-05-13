<?php

class CheckoutController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_users_svc = new Service_Users;
        $this->_messenger = $this->_helper->FlashMessenger;
        //$this->_messenger->setNamespace('checkout');
    }

    /**
     * Checkout form
     * 
     */
    public function indexAction() {
        // Check if XHR
        if ($this->_request->isXmlHttpRequest() &&
                !$this->_request->getParam('nolayout')) {
            $this->_updateCheckoutFormJson();
            return;
        }
        $cart = $this->_cart_svc->get();
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
        // User is logged out but is trying to purchase a renewal. Clear cart
        // show them what happened
        if ($cart->hasRenewal() && !$this->_users_svc->isAuthenticated()) {
            $this->_cart_svc->reset();
            $this->_forward('renewal-login-error');
            return;
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            $valid = $checkout_form->isValid($post);
            $this->_cart_svc->saveCheckoutForm($checkout_form, $post);
            if ($valid) {
                //
                if ($this->_cart_svc->process($checkout_form)) {
                    $this->_helper->Redirector->gotoSimple('confirmation');
                    return;
                } else {
                    $msg = 'There was a problem with your order. Please check ' .
                        'your information and try again.';
                    $this->_messenger->addMessage($msg);
                    $this->_helper->Redirector->gotoRoute(array(), 'checkout');
                    return;
                }
            } else {
                $this->_messenger->addMessage('Submitted information is not valid');
            }
            $this->view->messages = $this->_messenger->getCurrentMessages();
        } else {
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            $this->view->messages = $this->_messenger->getMessages();
        }
        $this->view->cart = $this->_cart_svc->get();
        $this->view->checkout_form = $checkout_form;
        $this->view->cart_totals = $this->_cart_svc->get()->getTotals();
        $this->view->inlineScriptMin()->loadGroup('checkout')
            ->appendScript("Pet.loadView('Checkout');");
    }

    private function _updateCheckoutFormJson() {
        if ($this->_request->isPost()) {
            $cart = $this->_cart_svc->get();
            if (!$cart->hasProducts() || $cart->hasRenewal() &&
                    !$this->_users_svc->isAuthenticated()) {
                $this->_helper->json(array(
                    'empty' => true
                ));
                return;
            }
            $fields = Zend_Json::decode($this->_request->getParam('model'));
            $post = array();
            foreach ($fields as $field) {
                $post[$field['name']] = $field['value'];
            }
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            $status = $checkout_form->isValid($post);
            $this->_cart_svc->saveCheckoutForm($checkout_form, $post);
            $this->_helper->json(array(
                'messages' => $checkout_form->getMessages(),
                'status' => $status
            ));
        }
    }

    /**
     * Confirmation page
     * 
     */
    public function confirmationAction() {
        $confirmation = $this->_cart_svc->getConfirmation();
        if (!$confirmation) {
            $this->_helper->Redirector->gotoSimple('index');
            return;
        }
        $this->view->cart = $confirmation->cart;
    }

    /**
     * Page that tells the user that they were trying to purchase a renewal, 
     * but they were logged out/timed out
     * 
     */
    public function renewalLoginErrorAction() {
        $this->view->getHelper('serverUrl')->setScheme('https');
    }

}
