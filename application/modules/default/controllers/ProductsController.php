<?php

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_svc = new Service_Products;
        $this->_users_svc = new Service_Users;
    }

    /**
     * Profile form for logged-in users
     * 
     */
    public function indexAction() {
    }

    /**
     * Regular subscriptions 
     * 
     */
    public function subscriptionsAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
    }

    /**
     * Subscription renewals
     * 
     */
    public function subscriptionRenewAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_forward('subscription-term-select', 'products',
                'default', array('is_renewal' => 1));
        } else {
            $this->_forward('login', 'profile', 'default', 
                array(
                    'redirect_to'     => 'products_subscription_select_term',
                    'redirect_params' => array('is_renewal' => 1)
                )
            );
        }
    }

    /**
     * Term select form, subscriptions, gift and non
     * 
     */
    public function subscriptionTermSelectAction() {
        $zone_id = $this->_request->getParam('zone_id');
        $is_gift    = $this->_request->getParam('is_gift');
        $is_renewal = $this->_request->getParam('is_renewal');
        // Attempt to get the user's zone from their profile if zone_id is not
        // passed
        if ($is_renewal && !$zone_id && $this->_users_svc->isAuthenticated()) {
            $profile = $this->_users_svc->getProfile();
            $sz = $this->_products_svc->getSubscriptionZoneByName(
                $profile->billing_country);
            if ($sz) {
                $zone_id = $sz->id;
            }
        }
        if (!$zone_id) {
            throw new Exception('Zone not defined');
        }
        $subs = $this->_products_svc->getSubscriptionsByZoneId(
            $zone_id, $is_gift, $is_renewal);
        if ($subs) {
            $form = $this->_products_svc->getSubscriptionTermSelectForm(
                $subs, $zone_id, $is_gift, $is_renewal);
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
     * Digital subscriptions 
     * 
     */
    public function digitalAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
    }

    /**
     * Subscription renewals
     * 
     */
    public function digitalRenewAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_forward('digital-select', 'products',
                'default', array('is_renewal' => 1));
        } else {
            $this->_forward('login', 'profile', 'default', 
                array(
                    'redirect_to'     => 'products_digital_select',
                    'redirect_params' => array('is_renewal' => 1)
                )
            );
        }
    }
    
    /**
     * Term select form, digital subscriptions
     * 
     * 
     */
    public function digitalSelectAction() {
        $is_gift    = $this->_request->getParam('is_gift');
        $is_renewal = $this->_request->getParam('is_renewal');
        $subs = $this->_products_svc->getDigitalSubscriptions($is_gift, $is_renewal);
        if ($subs) {
            $form = $this->_products_svc->getDigitalSubscriptionSelectForm($subs, $is_gift, $is_renewal);
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
            throw new Exception('Digital subscriptions not found'); 
        }
    }
    
    /**
     * Gift subscriptions
     * 
     */
    public function giftsAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }
    
    /**
     * Physical products
     *
     */
    public function physicalAction() {
        $this->view->products = $this->_products_svc->getPhysicalProducts(); 
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }
}
