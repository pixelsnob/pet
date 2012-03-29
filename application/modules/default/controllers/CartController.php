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
        print_r($session->cart->getProduct(3));
    }

    /**
     * 
     */
    public function addAction() {
        $product_id = $this->_request->getParam('product_id');
        $this->_cart_svc->addProduct($product_id);
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function removeAction() {

    }


}
