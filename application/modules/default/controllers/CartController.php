<?php

class CartController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        //$this->view->headLink()->appendStylesheet('/css/store.css');
    }

    /**
     * 
     */
    public function indexAction() {
        $session = new Zend_Session_Namespace;
        print_r($session->cart);
        exit;
    }

    /**
     * 
     */
    public function addAction() {
        $product_id = $this->_request->getParam('product_id');
        $this->_cart_svc->addProduct($product_id);
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

}
