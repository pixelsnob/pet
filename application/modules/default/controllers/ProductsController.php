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
    public function subscriptionAction() {
        
    }

    public function subscriptionSelectTermAction() {
        $zone_id = $this->_request->getParam('zone_id');
        $subs = $this->_products_svc->getSubscriptionsByZoneId($zone_id, false);
        if ($subs) {
            $this->view->select_term_form = $this->_products_svc
                ->getSubscriptionTermSelectForm($subs);
        } else {
            throw new Exception('Zone not found'); 
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
