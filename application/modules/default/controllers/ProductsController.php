<?php

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_svc = new Service_Products;
        //$this->view->headLink()->appendStylesheet('/css/store.css');
    }

    /**
     * Profile form for logged-in users
     * 
     */
    public function indexAction() {
        
    }

    /**
     * 
     */
    public function magazineAction() {
        
    }

    public function magazineSelectAction() {
        $product_id = $this->_request->getParam('product_id');
        $sub = $this->_products_svc->getSubscriptionByProductId($product_id);
        if ($sub) {
            print_r($sub); 
        } else {
            throw new Exception('Magazine not found'); 
        }
    }

    /**
     * 
     */
    public function digitalAction() {
        
    }

    /**
     * 
     */
    public function giftAction() {
    }
}
