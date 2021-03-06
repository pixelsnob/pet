<?php

require_once 'markdown.php';

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_mapper = new Model_Mapper_Products;
        $this->_users_svc = new Service_Users;
        $this->view->getHelper('serverUrl')->setScheme('https');
    }

    public function indexAction() {
        $this->view->suppress_nav = true;
        $this->view->suppress_top_bar = true;
        $this->view->body_id = 'products-index';
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    public function specialAction() {
        $this->view->suppress_nav = true;
        $this->view->suppress_top_bar = true;
        $this->view->body_id = 'products-special';
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    public function subscriptionOptionsAction() {
        $request = $this->getRequest();
        $zone_id = $request->getParam('zone_id');
        if (!$zone_id) {
            throw new Exception('Zone id is required');
        }
        $is_gift    = ($request->getParam('is_gift') ? true : null);
        $is_renewal = $request->getParam('is_renewal');
        $promo_code = $request->getParam('promo_code');
        $regular_subs = $this->_products_mapper->getSubscriptionsByZoneId(
            $zone_id, null, $is_gift, $is_renewal);
        $digital_subs = $this->_products_mapper->getDigitalSubscriptions(
            $is_gift, $is_renewal);
        $form = new Form_SubscriptionOptions(array(
            'subscriptions' => array_merge($regular_subs, $digital_subs)
        ));
        $form->populate($request->getParams());
        $this->view->regular_subs = $regular_subs;
        $this->view->digital_subs = $digital_subs;
        $this->view->sub_options_form = $form;
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $product_id = $request->getPost('product_id');
            $this->_helper->Redirector->setGotoSimple('add', 'cart',
                'default',  array(
                    'product_id' => $product_id,
                    'is_gift'    => $is_gift,
                    'is_renewal' => $is_renewal,
                    'promo_code' => $promo_code
                ));
        }
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    public function renewalOptionsAction() {
        $request = $this->getRequest();
        if ($this->_users_svc->isAuthenticated()) {
            $zone_id = $this->_users_svc->getZoneId();
            if (!$zone_id) {
                throw new Exception('Zone id not found for authenticated user');
            }
            $this->_forward('subscription-options', 'products', 'default',
                array('is_renewal' => true, 'zone_id' => $zone_id));
        } else {
            $this->_helper->FlashMessenger->setNamespace('login_form')
                ->addMessage('Please log in to renew your subscription');
            $params = $request->getParams();
            $params['is_renewal'] = 1;
            $this->_forward('login', 'profile', 'default', array(
                'redirect_to'     => 'products_renewal_options',
                'redirect_params' => $params
            ));
        }
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    public function physicalDetailAction() {
        $product_id = $this->_request->getParam('product_id');
        if (!$product_id) {
            throw new Exception('Product id is required');
        }
        $product = $this->_products_mapper->getById($product_id);
        if (!$product) {
            throw new Exception('Product not found');
        }
        $this->view->product = $product;
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    public function subscriptionZoneAction() {
        $this->view->is_gift = ($this->_request->getParam('is_gift') ? true : null);
        $promo_code = $this->_request->getParam('promo_code');
        $this->view->promo_code = $promo_code;
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        
    }

    public function giftsAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

}
