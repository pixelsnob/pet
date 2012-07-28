<?php

require_once 'markdown.php';

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_mapper = new Model_Mapper_Products;
        $this->_users_svc = new Service_Users;
    }

    /**
     * 
     */
    public function indexAction() {
        $this->view->suppress_nav = true;
        $this->view->suppress_top_bar = true;
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
        $is_gift = $this->_request->getParam('is_gift');
        $is_gift = (strlen(trim($is_gift)) ? true : null);
        $is_renewal = $this->_request->getParam('is_renewal');
        // Attempt to get the user's zone from their profile if zone_id is not
        // passed
        if ($is_renewal && !$zone_id && $this->_users_svc->isAuthenticated()) {
            $profile = $this->_users_svc->getProfile();
            $sz = $this->_products_mapper->getSubscriptionZoneByName(
                $profile->billing_country);
            if ($sz) {
                $zone_id = $sz->id;
            }
        }
        if (!$zone_id) {
            throw new Exception('Zone not defined');
        }
        $subs = $this->_products_mapper->getSubscriptionsByZoneId(
            $zone_id, $is_gift, $is_renewal);
        if ($subs) {
            $form = new Form_SubscriptionTermSelect(array(
                'zoneId'  => $zone_id,
                'isGift'    => $is_gift,
                'isRenewal' => $is_renewal
            ));
            $opts = array();
            foreach ($subs as $sub) {
                $opts[$sub->product_id] = $sub->name . ($is_gift ? ' (gift)' : '');
            }
            $form->product_id->setMultiOptions($opts);
            $post = $this->_request->getPost();
            if ($this->_request->isPost() && $form->isValid($post)) {
                $product_id = $this->_request->getPost('product_id');
                $this->_helper->Redirector->setGotoSimple('add', 'cart',
                    'default',  array(
                        'product_id' => $product_id,
                        'is_gift' => $is_gift
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
        $is_gift = (strlen(trim($is_gift)) ? true : null);
        $is_renewal = $this->_request->getParam('is_renewal');
        $subs = $this->_products_mapper->getDigitalSubscriptions($is_gift,
            $is_renewal);
        if ($subs) {
            $form = new Form_DigitalSubscriptionSelect(array(
                'isGift'    => $is_gift,
                'isRenewal' => $is_renewal
            ));
            $opts = array();
            foreach ($subs as $sub) {
                $opts[$sub->product_id] = $sub->name . ($is_gift ? ' (gift)' : '');
            }
            $form->product_id->setMultiOptions($opts);

            $post = $this->_request->getPost();
            if ($this->_request->isPost() && $form->isValid($post)) {
                $product_id = $this->_request->getPost('product_id');
                $this->_helper->Redirector->setGotoSimple('add', 'cart',
                    'default',  array(
                        'product_id' => $product_id,
                        'is_gift' => $is_gift
                    )
                );
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
        $this->view->products = $this->_products_mapper->getPhysicalProducts(); 
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }
}
