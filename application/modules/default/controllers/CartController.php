<?php

class CartController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_messages = $this->_helper->FlashMessenger;
        //$this->view->headLink()->appendStylesheet('/css/store.css');
    }

    /**
     * 
     */
    public function indexAction() {
        echo '<pre>';
        $cart = $this->_cart_svc->get();
        print_r($this->_helper->FlashMessenger->getMessages());
        print_r($cart->getTotals());
        print_r($cart);
        echo '</pre>';
        exit;
    }

    /**
     * 
     */
    public function addAction() {
        $product_id = $this->_request->getParam('product_id');
        $this->_cart_svc->addProduct($product_id);
        $this->_messages->addMessage($this->_cart_svc->getMessage());
        $this->_helper->Redirector->setGotoSimple('index');
    }
    
    public function addPromoAction() {
        $code = $this->_request->getParam('code');
        $this->_cart_svc->addPromo($code);
        $this->_messages->addMessage($this->_cart_svc->getMessage());
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
        $this->_messages->addMessage($this->_cart_svc->getMessage());
        $this->_helper->Redirector->setGotoSimple('index');
    }
    
    public function resetAction() {
        $this->_cart_svc->reset();
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function test1Action() {
        $cart = $this->_cart_svc->get();
        echo '<pre>';
        foreach ($cart->products as $product) {
            echo $product->name . ' ' . $product->qty . "\n";

        }
        echo "\nsubscription: " . $cart->hasSubscription() . "\n";
        echo 'download: ' . $cart->hasDownload();
        echo '</pre>';
        exit;
    }

}
