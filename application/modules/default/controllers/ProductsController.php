<?php

require_once 'markdown.php';

class ProductsController extends Zend_Controller_Action {

    public function init() {
        $this->_products_mapper = new Model_Mapper_Products;
        $this->_users_svc = new Service_Users;
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
        $this->view->is_gift       = $is_gift;
        $this->view->is_renewal    = $is_renewal;
        $this->view->promo_code = $promo_code;
        if ($zone_id == Model_SubscriptionZone::USA) {
            $this->view->subscriptions = $this->_products_mapper
                ->getSubscriptionsByZoneId(Model_SubscriptionZone::USA,
                    $is_gift, $is_renewal); 
            $this->_helper->ViewRenderer->render('subscription-options-usa');
        } else {
            $regular_subs = $this->_products_mapper->getSubscriptionsByZoneId(
                $zone_id, $is_gift, $is_renewal);
            $digital_subs = $this->_products_mapper->getDigitalSubscriptions(
                $is_gift, $is_renewal);
            $form = new Form_SubscriptionOptions(array(
                'zoneId'        => $zone_id,
                'isGift'        => $is_gift,
                'isRenewal'     => $is_renewal,
                'subscriptions' => array_merge($regular_subs, $digital_subs),
                'promoCode'     => $promo_code
            ));
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
            $this->_helper->ViewRenderer->render('subscription-options-non-usa');
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
        $term = $this->_request->getParam('term');
        if (!$term) {
            throw new Exception('Term is required');
        }
        $promo_code = $this->_request->getParam('promo_code');
        $this->view->term = $term;
        $this->view->promo_code = $promo_code;
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        
    }

    public function giftsAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }

    /*
    public function subscriptionsAction() {
            $zone_id = $this->_users_svc->getZoneId();
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
    }

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

    public function digitalAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
        $this->view->is_authenticated = $this->_users_svc->isAuthenticated();
    }

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
    
    public function giftsAction() {
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }
    
    public function physicalAction() {
        $this->view->products = $this->_products_mapper->getPhysicalProducts(); 
        $this->view->inlineScriptMin()->loadGroup('products')
            ->appendScript("Pet.loadView('Products');");
    }*/
}
