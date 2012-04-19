<?php

class CartController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
    }

    /**
     * 
     */
    public function indexAction() {
        if ($this->_request->isXmlHttpRequest() &&
                !$this->_request->getParam('nolayout')) {
            $this->_helper->json($this->_cart_svc->get()->toArray());
        }
        $messenger = Zend_Registry::get('messenger');
        $messenger->setNamespace('cart');
        $this->view->cart = $this->_cart_svc->get();
        $cart_form = $this->_cart_svc->getCartForm();
        $this->view->cart_form = $cart_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
            $messenger->clearMessages(); 
            $this->view->use_current_messages = true;
            if ($cart_form->isValid($post)) {
                $this->_cart_svc->update($post);
            } else {
                $messenger->addMessage('Submitted information is not valid');
            }
        }
        $this->view->nolayout = $this->_request->getParam('nolayout');
    }

    /**
     * 
     */
    public function addAction() {
        $product_id = $this->_request->getParam('product_id');
        $this->_cart_svc->addProduct($product_id);
        $this->_helper->Redirector->setGotoSimple('index');
    }
    
    public function addPromoAction() {
        $code = $this->_request->getParam('code');
        $this->_cart_svc->addPromo($code);
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function setQtyAction() {
        $product_id = $this->_request->getParam('product_id');
        $qty = (int) $this->_request->getParam('qty');
        $this->_cart_svc->setProductQty($product_id, $qty);
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function removeAction() {
        $product_id = $this->_request->getParam('product_id');
        $this->_cart_svc->removeProduct($product_id);
        $this->_helper->Redirector->setGotoSimple('index');
    }
    
    public function resetAction() {
        $this->_cart_svc->reset();
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function test1Action() {
        $cart = $this->_cart_svc->get();
        echo '<pre>';
        print_r($cart);
        print_r($cart->getTotals());
        foreach ($cart->products as $product) {
            echo $product->name . ' ' . $product->qty . "\n";

        }
        echo "\nsubscription: " . $cart->hasSubscription() . "\n";
        echo 'download: ' . $cart->hasDownload();
        echo '</pre>';
        exit;
    }

    public function test2Action() {
    }

}
