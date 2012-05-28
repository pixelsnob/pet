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
    public function addProduct($product_id, $is_gift = false) {
        $product = $this->_products_svc->getById($product_id);
        if ($product) {
            if (!$this->_cart->addProduct($product, $is_gift)) {
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
     * @param string $key
     * @param int $qty
     * @return void
     * 
     */
    public function setProductQty($key, $qty) {
        $this->_cart->setProductQty($key, $qty);
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeProduct($key) {
        $this->_cart->removeProduct($key);
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
            $form_data['qty'][$product->key] = $product->qty;
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
        $promo_code = $form->promo->promo_code->getValue();
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
        $ot_mapper = new Model_Mapper_OrderTransactions;
        $config    = Zend_Registry::get('app_config');
        $logger    = Zend_Registry::get('log');
        // Operate on a copy of cart -- we don't want to modify it in here
        $cart      = clone $this->get();
        $totals    = $cart->getTotals();
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
        $db     = Zend_Db_Table::getDefaultAdapter();
        try {
            $db->beginTransaction();
            // Regular sale
            if ($config['use_payment_gateway']) {
                if ($cart->payment->payment_method == 'credit_card') {
                    $this->_gateway->processSale($data);
                } else {
                    $this->_gateway->processECSale($data, $cart->ec_token,
                        $payer_id);
                }
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
            $this->_saveOrderedProducts($data);
            $this->_saveOrderPayments($data);
            // Log
            $log_data = array(
                'type'     => 'process',
                'cart'     => $cart->toArray(),
                'order_id' => $data['order_id'],
                'user_id'  => $data['user_id']
            );
            $ot_mapper->insert(
                $status,
                $log_data,
                $this->_gateway->getRawCalls()
            );
            $db->commit();
        } catch (Exception $e) {
            $status = false;
            $log_data = array(
                'type'     => 'process',
                'cart'     => $cart->toArray(),
                // Ids may not exist here
                'order_id' => (isset($data['order_id']) ? $data['order_id'] :
                              null),
                'user_id'  => (isset($data['user_id']) ? $data['user_id'] :
                              null)
            );
            // These should fail silently if they do fail
            try {
                $this->_gateway->voidCalls();
                // Log
                $ot_mapper->insert(
                    $status,
                    $log_data,
                    $this->_gateway->getRawCalls(),
                    array($e->getMessage() . ' -- ' . $e->getTraceAsString())
                );
                print_r($e); exit;
            } catch (Exception $e1) {}
        }
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
     * @param array $data
     * @return void
     * 
     */
    private function _saveOrderedProducts(array $data) {
        $users_svc  = new Service_Users;
        $is_auth    = $users_svc->isAuthenticated();
        $user_id    = $users_svc->getId();
        $cart       = clone $this->get();
        $op         = new Model_Mapper_OrderedProducts;
        $os         = new Model_Mapper_OrderSubscriptions;
        $fmt        = 'Y-m-d H:i:s'; 
        foreach ($cart->products as $product) {
            // Insert into ordered_products
            $opid = $op->insert($product->toArray(), $data['order_id']); // <<<<<<<<<<<<<<< need to figure out discount cost stuff
            // Gift processing here
            if ($product->isGift()) {
                continue;
            } 
            if ($product->isSubscription()) {
                $expiration = null;
                if ($product->isRenewal() && $is_auth) {
                    $sub = $os->getUnexpiredByUserId($user_id, false, true);
                    if ($sub) {
                        $expiration = $sub->expiration;
                    }
                }
                $term = (int) $product->term_months;
                // If expiration is null here, DateTime defaults to today
                $date = new DateTime($expiration);
                $date->add(new DateInterval("P{$term}M"));
                $os->insert(array(
                    'user_id'            => $data['user_id'],
                    'order_id'           => $data['order_id'],
                    'expiration'         => $date->format($fmt)
                ));
            } elseif ($product->isDigital()) {
                $expiration = null;
                if ($product->isRenewal() && $is_auth) {
                    // To calculate new expiration, check to see if they have
                    // a regular sub, or a digital only sub, and use the
                    // greater, if any
                    $regular_sub = $os->getUnexpiredByUserId($user_id,
                        false, true);
                    $digital_sub = $os->getUnexpiredByUserId($user_id,
                        true, true);
                    // Digital sub, no regular sub
                    if ($digital_sub && !$regular_sub) {
                        $expiration = $digital_sub->expiration;
                    // Regular sub, no digital sub
                    } elseif (!$digital_sub && $regular_sub) {
                        $expiration = $regular_sub->expiration;
                    // Both regular and digital subs: calculate greater
                    } elseif ($digital_sub && $regular_sub) {
                        $digital_exp = new DateTime($digital_sub->expiration);
                        $regular_exp = new DateTime($regular_sub->expiration);
                        $date_interval = $digital_exp->diff($regular_exp);
                        if ($date_interval->days == 0 || $date_interval->invert) {
                            // Digital sub has greater expiration date
                            $expiration = $digital_sub->expiration;
                        } else {
                            // Regular sub has greater expiration date
                            $expiration = $regular_sub->expiration;
                        }
                    }
                }
                $term = (int) $product->term_months;
                // If expiration is null here, DateTime defaults to today
                $date = new DateTime($expiration);
                $date->add(new DateInterval("P{$term}M"));
                $os->insert(array(
                    'user_id'            => $data['user_id'],
                    'order_id'           => $data['order_id'],
                    'expiration'         => $date->format($fmt),
                    'digital_only'       => 1
                ));
            }
        }
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    private function _saveOrderPayments($data) {
        $gateway_responses = $this->_gateway->getSuccessfulResponseObjects();
        $op_mapper = new Model_Mapper_OrderPayments;
        foreach ($gateway_responses as $response) {
            if (is_a($response, 'Model_PaymentGateway_Response_Payflow')) {
                $opid = $op_mapper->insert(array(
                    'order_id'            => $data['order_id'],
                    'payment_type_id'     => Model_PaymentType::PAYFLOW,
                    'amount'              => $data['total'],
                    'date'                => date('Y-m-d H:i:s')
                ));
                $op_payflow_mapper = new Model_Mapper_OrderPayments_Payflow;
                $op_payflow_mapper->insert(array(
                    'order_payment_id'    => $opid,
                    'cc_number'           => substr($data['cc_num'], -4),
                    'cc_expiration_month' => $data['cc_exp_month'],
                    'cc_expiration_year'  => $data['cc_exp_year'],
                    'pnref'               => $response->pnref,
                    'ppref'               => $response->ppref,
                    'correlationid'       => $response->correlationid,
                    'cvv2match'           => $response->cvv2match

                ));
            } elseif (is_a($response, 'Model_PaymentGateway_Response_Paypal')) {
                $opid = $op_mapper->insert(array(
                    'order_id'         => $data['order_id'],
                    'payment_type_id'  => Model_PaymentType::PAYPAL,
                    'amount'           => $data['total'],
                    'date'             => date('Y-m-d H:i:s')
                ));
                $op_paypal_mapper = new Model_Mapper_OrderPayments_Paypal;
                $op_paypal_mapper->insert(array(
                    'order_payment_id' => $opid,
                    'correlationid'    => $response->correlationid
                ));
            }
        }
    }
    
    /**
     * @param string $return_url The url the customer is returned to if success
     * @param string $cancel_url The url the customer is returned to if fail
     * @return string 
     * 
     */
    public function getECUrl($return_url, $cancel_url) {
        $ot_mapper = new Model_Mapper_OrderTransactions;
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
        try {
            $token = $this->_gateway->getECToken($data, $return_url, $cancel_url);
            if (!$token) {
                throw new Exception(__FUNCTION__ . '() failed');
            }
            $cart->ec_token = $token;
            $ot_mapper->insert(
                $status,
                array('cart' => $cart->toArray()),
                $this->_gateway->getRawCalls()
            );
        } catch (Exception $e) {
            $status = false;
            try {
                $ot_mapper->insert(
                    $status,
                    array('cart' => $cart->toArray()),
                    $this->_gateway->getRawCalls(),
                    array($e->getMessage())
                );
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
    
}
