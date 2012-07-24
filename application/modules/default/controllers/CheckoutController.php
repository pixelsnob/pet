<?php

require_once 'markdown.php';

class CheckoutController extends Zend_Controller_Action {

    private $_generic_error = '';

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_cart_mapper = new Model_Mapper_Cart;
        $this->_users_svc = new Service_Users;
        $this->_messenger = $this->_helper->FlashMessenger;
        $this->_messenger->setNamespace('checkout');
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_generic_error = 'There was a problem with your order. Please check ' .
            'your information and try again.';
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
        ///////////////////////////////////////////////////////////////////////
        $cart = $this->_cart_mapper->get();
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
        // User is logged out but is trying to purchase a renewal. Clear cart
        // show them what happened
        if ($cart->products->hasRenewal() && !$this->_users_svc->isAuthenticated()) {
            $this->_cart_mapper->reset();
            $this->_forward('renewal-login-error');
            return;
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            $valid = $checkout_form->isValid($post);
            $this->_cart_svc->saveCheckoutForm($checkout_form, $post);
            if ($valid) {
                $cart = $this->_cart_mapper->get();
                if ($cart->payment->payment_method == 'credit_card') {
                    // Credit card transactions
                    if ($this->_cart_svc->process($checkout_form)) {
                        $this->_helper->Redirector->gotoSimple('confirmation');
                        exit;
                    } else {
                        $this->_messenger->addMessage($this->_generic_error);
                        $this->_helper->Redirector->gotoRoute(array(), 'checkout');
                        exit;
                    }
                } else {
                    // Paypal Express Checkout -- redirect to paypal site
                    $return_url = $this->view->serverUrl($this->view->url(
                        array(), 'checkout_process_paypal'));
                    $cancel_url = $this->view->serverUrl($this->view->url(
                        array(), 'checkout'));
                    $url = $this->_cart_svc->getECUrl(
                        $return_url, $cancel_url);
                    if (!$url) {
                        $this->_messenger->addMessage($this->_generic_error);
                        $this->_helper->Redirector->gotoRoute(array(), 'checkout');
                        exit;
                    }
                    $this->_helper->Redirector->gotoUrl($url);
                    exit;
                }
            } else {
                $this->_messenger->addMessage('Submitted information is not valid');
            }
            $this->view->messages = $this->_messenger->getCurrentMessages();
        } else {
            $checkout_form = $this->_cart_svc->getCheckoutForm();
            $this->view->messages = $this->_messenger->getMessages();
        }
        $this->view->cart = $this->_cart_mapper->get();
        $this->view->checkout_form = $checkout_form;
        $this->view->cart_totals = $this->_cart_mapper->get()->getTotals();
        $this->view->inlineScriptMin()->loadGroup('checkout')
            ->appendScript("Pet.loadView('Checkout');");
    }
    
    /**
     * Processes form post and returns json
     * 
     */
    private function _updateCheckoutFormJson() {
        if ($this->_request->isPost()) {
            $cart = $this->_cart_mapper->get();
            if (!count($cart->products) || $cart->products->hasRenewal() &&
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
    
    public function processPaypalAction() {
        $cart = $this->_cart_mapper->get();
        $token = $this->_request->getParam('token');
        $payer_id = $this->_request->getParam('PayerID'); // Notice capitalization!
        // Make sure token and payer id exist, and validate token against
        // token stored in cart
        if (!$token || !$payer_id || $token != $cart->ec_token) {
            $this->_messenger->addMessage($this->_generic_error);
            $this->_helper->Redirector->gotoRoute(array(), 'checkout');
            exit;
        }
        // Validate stored values
        $checkout_form = $this->_cart_svc->getCheckoutForm();
        if ($this->_cart_svc->validateSavedForm($checkout_form)) {
            if ($this->_cart_svc->process($checkout_form, $payer_id)) {
                $this->_helper->Redirector->gotoSimple('confirmation');
                exit;
            } else {
                $this->_messenger->addMessage($this->_generic_error);
                $this->_helper->Redirector->gotoRoute(array(), 'checkout');
            }
        } else {
            $this->_messenger->addMessage('Submitted information is not valid');
            //print_r($checkout_form->getMessages()); exit;
            $this->_helper->Redirector->gotoRoute(array(), 'checkout');
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

    /*public function testAction() {
        $mongo = Pet_Mongo::getInstance();
        for ($i = 0; $i < 10000; $i++) {
            $mongo->testing->insert(array(
                'test' => 'xxxxxxxxxxxxxxxxxxxxx',
                'i'    => $i
            ), array('fsync' => false));
        }
        exit('?');
    }*/

}
