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
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript('new Pet.ProductsView; new Pet.CartView;');
        $this->view->gift = $this->_request->getParam('gift');
    }

    public function subscriptionSelectTermAction() {
        $zone_id = $this->_request->getParam('zone_id');
        $gift = $this->_request->getParam('gift');
        $subs = $this->_products_svc->getSubscriptionsByZoneId($zone_id, false);
        if ($subs) {
            $form = $this->_products_svc->getSubscriptionTermSelectForm(
                $subs, $zone_id, $gift);
            $post = $this->_request->getPost();
            if ($this->_request->isPost() && $form->isValid($post)) {
                $product_id = $this->_request->getPost('product_id');
                $this->_helper->Redirector->setGotoSimple('add', 'cart',
                    'default',  array(
                        'product_id' => $product_id,
                        'gift' => $gift
                    ));
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
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript('new Pet.ProductsView; new Pet.CartView;');
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

}
