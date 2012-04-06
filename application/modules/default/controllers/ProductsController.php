<?php

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_svc = new Service_Products;
        //$this->view->headLink()->appendStylesheet('/css/store.css');
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript('new Pet.ProductsView;');
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
            $form = $this->_products_svc->getSubscriptionTermSelectForm($subs);
            $post = $this->_request->getPost();
            if ($this->_request->isPost() && $form->isValid($post)) {
                $product_id = $this->_request->getPost('product_id');
                $this->_helper->Redirector->setGotoSimple('add', 'cart',
                    'default',  array('product_id' => $product_id));
            } else {
                $form->populate($post);
            }
            $this->view->select_term_form = $form;
        } else {
            throw new Exception('Zone not found'); 
        }
    }

    /**
     * 
     */
    public function digitalAction() {
        
    }
    
    public function digitalSelectAction() {
        $subs = $this->_products_svc->getDigitalSubscriptions();
        if ($subs) {
            $form = $this->_products_svc->getDigitalSubscriptionSelectForm($subs);
            $post = $this->_request->getPost();
            if ($this->_request->isPost() && $form->isValid($post)) {
                $product_id = $this->_request->getPost('product_id');
                $this->_helper->Redirector->setGotoSimple('add', 'cart',
                    'default',  array('product_id' => $product_id));
            } else {
                $form->populate($post);
            }
            $this->view->digital_select_form = $form;
        } else {
            throw new Exception('Zone not found'); 
        }
    }

    /**
     * 
     */
    public function giftAction() {
    }
}
