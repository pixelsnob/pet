<?php

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_svc = new Service_Products;
        $this->_users_svc = new Service_Users;
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
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
    }

    public function subscriptionRenewAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_forward('subscription-term-select', 'products',
                'default', array('renewal' => 1));
        } else {
            $this->_forward('login', 'profile', 'default', 
                array(
                    'redirect_to'     => 'products_subscription_term_select',
                    'redirect_params' => array('renewal' => 1)
                )
            );
        }
    }

    public function subscriptionTermSelectAction() {
        $zone_id = $this->_request->getParam('zone_id');
        $gift    = $this->_request->getParam('gift');
        $renewal = (bool) $this->_request->getParam('renewal');
        // Attempt to get the user's zone from their profile if zone_id is not
        // passed
        if ($renewal && !$zone_id && $this->_users_svc->isAuthenticated()) {
            $profile = $this->_users_svc->getProfile();
            $sz = $this->_products_svc->getSubscriptionZoneByName(
                $profile->billing_country);
            if ($sz) {
                $zone_id = $sz->id;
            }
        }
        $subs = $this->_products_svc->getSubscriptionsByZoneId($zone_id, $renewal);
        if ($subs) {
            $form = $this->_products_svc->getSubscriptionTermSelectForm(
                $subs, $zone_id, $gift, $renewal);
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
