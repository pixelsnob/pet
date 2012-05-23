<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
class Service_Cart {
    
    /**
     * @param string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_cart = new Model_Mapper_Cart;
        $this->_products_svc = new Service_Products;
        $this->_gateway = new Model_Mapper_PaymentGateway;
    }
    
    /**
     * @return Model_Cart
     * 
     */
    public function get() {
        return $this->_cart->get();
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function update(array $data) {
        $this->_cart->update($data);
    }

    /**
     * @param int $product_id
     * @param int $is_gift
     * @return bool
     * 
     */
    public function addProduct($product_id) {
        $product = $this->_products_svc->getById($product_id);
        if ($product) {
            if (!$this->_cart->addProduct($product)) {
                $this->_message = $this->_cart->getMessage();
                return false;
            }
        } else {
            $this->_message = 'Product not found';
            return false;
        }
        return true;
    }
    
    /**
     * @param int $product_id
     * @param int $qty
     * @return void
     * 
     */
    public function setProductQty($product_id, $qty) {
        $this->_cart->setProductQty($product_id, $qty);
    }

    /**
     * @param int $product_id
     * @return void
     */
    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id);
    }

    /**
     * @return void
     */
    public function reset() {
        $this->_cart->reset();
    }

    /**
     * @param string $code
     * @return void
     * 
     */
    public function addPromo($code) {
        $cart = $this->_cart->get();
        if (!strlen(trim($code))) {
            if ($cart->promo) {
                $this->_message = 'Promo removed';
                $this->_cart->removePromo();
            }
            return true;
        }
        $promo_svc = new Service_Promos;
        $promo = $promo_svc->getUnexpiredPromoByCode($code);
        if ($promo && $this->_cart->addPromo($promo)) {
            $this->_message = "Promo $code added";
            return true;
        } else {
            $this->_message = 'Promo is not valid';
            return false;
        }
    }
    
    /**
     * @return void
     * 
     */
    public function removePromo() {
        $this->_cart->removePromo();
    }

    /**
     * @return Form_Cart
     * 
     */
    public function getCartForm() {
        $cart = $this->_cart->get();
        $form = new Form_Cart(array(
            'cart' => $cart
        ));
        $form_data = array();
        foreach ($cart->products as $product) {
            $form_data['qty'][$product->product_id] = $product->qty;
        }
        $form->populate($form_data);
        return $form;
    }

    /**
     * @return Form_Checkout
     * 
     */
    public function getCheckoutForm() {
        $cart = $this->_cart->get();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $states = new Zend_Config(require APPLICATION_PATH .
            '/configs/states.php');
        $countries = new Zend_Config(require APPLICATION_PATH .
            '/configs/countries.php');
        $form = new Form_Checkout(array(
            'identity'  => $identity,
            'users'     => new Model_Mapper_Users,
            'states'    => $states->toArray(),
            'countries' => $countries->toArray(),
            'cart'      => $cart,
            'promos'    => new Model_Mapper_Promos
        ));
        $users_svc = new Service_Users;
        // We don't need pw/username fields if user is already logged in
        if ($users_svc->isAuthenticated()) {
            $form->user->username->setValidators(array())->setRequired(false);
            $form->user->password->setValidators(array())->setRequired(false);
            $form->user->confirm_password->setValidators(array())
                ->setRequired(false);
        }
        $form_data = array_merge(
            $cart->billing->toArray(),
            $cart->shipping->toArray(),
            $cart->payment->toArray(),
            array('use_shipping' => $cart->use_shipping)
        );
        // If user is logged in, use that data to populate form, otherwise
        // show saved data if any
        if ($users_svc->isAuthenticated()) {
            $form_data = array_merge(
                $form_data,
                $users_svc->getUser()->toArray(),
                $users_svc->getProfile()->toArray()
            );
        } else {
            $form_data = array_merge(
                $form_data,
                $cart->user->toArray(),
                $cart->user_info->toArray()
            );
        }
        if ($cart->promo) {
            $form_data = array_merge($form_data, array('promo_code' =>
                $cart->promo->code));
        }
        $form->populate($form_data);
        return $form;
    }
    
    /**
     * @param Form_Checkout $form
     * @param array $data
     * @return void
     * 
     */
    public function saveCheckoutForm(Form_Checkout $form, array $data) {
        $cart = $this->_cart->get();
        $this->_cart->setBilling($form->billing->getValues(true));
        $this->_cart->setShipping($form->getShippingValues());
        $this->_cart->setUser($form->user->getValues(true));
        $this->_cart->setUserInfo($form->info->getValues(true));
        $this->_cart->setPayment($form->payment->getValues(true));
        $promo_code = $form->promo->promo_code;
        $existing_promo_code = ($cart->promo ? $cart->promo->code : '');
        if ($promo_code && $promo_code != $existing_promo_code) {
            $promos_mapper = new Model_Mapper_Promos;
            $promo = $promos_mapper->getUnexpiredPromoByCode($promo_code);
            if ($promo) {
                $this->_cart->addPromo($promo);
            }
        } elseif (!strlen(trim($promo_code)) && $existing_promo_code) {
            $this->_cart->removePromo();
        }
        $use_shipping = (isset($data['use_shipping']) ?
            (int) $data['use_shipping'] : 0);
        $this->_cart->setUseShipping($use_shipping);
    }
    
    /**
     * @param Form_Checkout $form
     * @return bool
     * 
     */
    public function validateSavedForm(Form_Checkout $form) {
        $data = array_merge(
            $form->billing->getValues(true),
            $form->payment->getValues(true),
            $form->user->getValues(true),
            $form->getShippingValues(),
            array('promo_code' => $form->promo->promo_code->getValue())
        );
        // Remove pw validators
        $form->user->password->setValidators(array())->setRequired(false);
        $form->user->confirm_password->setValidators(array())
            ->setRequired(false);
        return $form->isValid($data); 
    }

    /**
     * @param array $post
     * @param string $payer_id
     * @return bool
     * 
     */
    public function process($form, $payer_id = '') {
        $users_svc = new Service_Users;
        $config = Zend_Registry::get('app_config');
        $logger = Zend_Registry::get('log');
        // Operate on a copy of cart -- we don't want to modify it in here
        $cart = clone $this->get();
        $totals = $cart->getTotals();
        // Merge input data into one array
        $data = array_merge(
            $form->billing->getValues(true),
            $form->payment->getValues(true),
            array('total' => $totals['total']),
            $form->user->getValues(true),
            $form->getShippingValues(),
            $form->info->getValues(true),
            array(
                'promo_id'       => ($cart->promo ? $cart->promo->id : null),
                'old_expiration' => null
            )
        );
        $status = true;
        $exceptions = array();
        $db = Zend_Db_Table::getDefaultAdapter();
            
        try {
            if (!$cart->validate()) {
                throw new Exception($cart->getMessage());
            }
            $db->beginTransaction();
            // Get existing subscriptions, if any
            /*if ($cart->products->hasRenewal()) {
                if ($cart->products->hasSubscription()) {
                    // Regular sub
                    $ops_mapper = new Model_Mapper_OrderedProductSubscriptions; 
                    $sub = $ops_mapper->getUnexpiredByUserId(
                        $users_svc->getId(), true);
                    $data['old_expiration'] = $sub->expiration;
                } elseif ($cart->products->hasDigitalSubscription()) {
                    // Digital sub
                    $opds_mapper = new Model_Mapper_OrderedProductDigitalSubscriptions; 
                    $sub = $opds_mapper->getUnexpiredByUserId(
                        $users_svc->getId(), true);
                    $data['old_expiration'] = $sub->expiration;
                }
                if (!$data['old_expiration']) {
                    $msg = 'Attempted a renewal but existing subscription ' . 
                            'expiration was not found';
                    throw new Exception($msg);
                }
            }*/
            // Recurring billing
            /*if ($cart->products->hasRecurring()) {
                foreach ($cart->products as $product) {
                    if (!$product->is_recurring) {
                        continue;
                    }
                    $data['cost']        = $product->cost;
                    $data['term']        = $product->term;
                    $data['description'] = $product->name;
                    $data['profile_id']  = uniqid();
                    if ($product->isRenewal()) {
                        // get existing paypal profileid
                        // attempt to update pp profile
                        exit('not yet');
                    } else {
                        if ($cart->payment->payment_method == 'credit_card') {
                            $this->_gateway->processRecurringPayment($data);
                        } else {
                            $this->_gateway->processECRecurringPayment(
                                $data, $cart->ec_token, $payer_id);
                        }
                    }
                }
            }*/
            // Regular sale
            if ($cart->payment->payment_method == 'credit_card') {
                $this->_gateway->processSale($data);
            } else {
                $this->_gateway->processECSale($data, $cart->ec_token,
                    $payer_id);
            }
            // Save user data
            if ($users_svc->isAuthenticated()) {
                // update email 
                $data['user_id'] = $users_svc->getId();
            } else {
                $user = new Model_Mapper_Users;
                $data['user_id'] = $user->insert($data);
                if (!$data['user_id']) {
                    throw new Exception('user_id not defined');
                }
            }
            // Save order data
            $order = new Model_Mapper_Orders;
            $data['order_id'] = $order->insert($data);
            // Save products
            $ordered_product = new Model_Mapper_OrderedProducts;
            foreach ($cart->products as $product) {
                // Insert into ordered_products
                $opid = $ordered_product->insert($product->toArray(),
                    $data['order_id']);                                                // <<<<<<<<<<<<<<< need to figure out discount cost stuff
                // Gift processing here
                if ($product->isGift()) {
                    
                }
                if ($product->isDigital()) {
                    // add to ordered_product_digital_subscriptions
                } elseif ($product->isSubscription()) {
                    // add to product_digital_subscriptions
                }
            }
            // Log
            $this->_logTransaction('orders', $status, $cart->toArray());
            $db->commit();
        } catch (Exception $e) {
            $status = false;
            $exceptions[] = $e->getMessage();
            try {
                $this->_gateway->voidCalls();
                // Log
                $this->_logTransaction('orders', $status, $cart->toArray(),
                    $exceptions);
            } catch (Exception $e2) {
                //print_r($e2); exit;
            }
        }
        $this->_gateway->voidCalls();
        // Reset cart
        if ($status) {
            $this->_cart->setConfirmation($this->_cart->get());
            if ($config['reset_cart_after_process']) {
                $this->_cart->reset();
            }
        }
        return $status;
    }
    
    /**
     * @param string $return_url The url the customer is returned to if success
     * @param string $cancel_url The url the customer is returned to if fail
     * @return string 
     * 
     */
    public function getECUrl($return_url, $cancel_url) {
        $config = Zend_Registry::get('app_config');
        $cart = $this->get();
        $totals = $cart->getTotals();
        $data = array(
            'email'        => $cart->user->email,
            'total'        => $totals['total'],
            'return_url'   => $return_url,
            'cancel_url'   => $cancel_url,
            'products'     => $cart->products->toArray()
        );
        $status = true;
        $exceptions = array();
        try {
            $token = $this->_gateway->getECToken($data, $return_url, $cancel_url);
            if (!$token) {
                throw new Exception(__FUNCTION__ . '() failed');
            }
            $cart->ec_token = $token;
            $this->_logTransaction('ec_transactions', $status, $cart->toArray());
        } catch (Exception $e) {
            $status = false;
            $exceptions[] = $e->getMessage();
            try {
                $this->_logTransaction('ec_transactions', $status,
                    $cart->toArray(), $exceptions);
            } catch (Exception $e2) {}
        }
        if ($status) {
            return $config['payment_gateway']['ec_url'] . '&token=' . $token;
        }
    }

    /**
     * return Model_Confirmation|void
     * 
     */
    public function getConfirmation() {
        return $this->_cart->getConfirmation();
    }

    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
    
    /**
     * @param string $collection The mongo collection to insert to
     * @param bool $status
     * @param array $data
     * @param array $exceptions 
     * 
     */
    private function _logTransaction($collection, $status, array $data,
                                     array $exceptions = array()) {
        $mongo = Pet_Mongo::getInstance();
        $data = array_merge(array(
            'timestamp'        => time(),
            'date_r'           => date('Y-m-d H:i:s'),
            'status'           => ($status ? 'success' : 'failed')
        ), $data);
        $data = array_merge($data, array(
            'gateway_calls'    => $this->_gateway->getCalls(),
            'exceptions'       => $exceptions
        ));
        $mongo->{$collection}->insert($data, array('fsync' => true));
    }
    
}
