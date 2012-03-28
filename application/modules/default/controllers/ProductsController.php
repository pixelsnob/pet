<?php

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
    }

    /**
     * Profile form for logged-in users
     * 
     */
    public function indexAction() {
        //$this->view->headLink()->appendStylesheet('/css/profile.css');
    }
}
