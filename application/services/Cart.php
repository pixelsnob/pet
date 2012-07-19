<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
require_once 'TokenGenerator.php';

class Service_Cart {
    
    /**
     * @var string
     * 
     */
    protected $_message = '';

    /**
     * @var null|Model_Cart_Order
     * 
     */
    protected $_order;

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_cart_mapper = new Model_Mapper_Cart;
        $this->_gateway = new Model_Mapper_PaymentGateway;
    }
    
    /**
     * @param string $token
     * @return bool
     * 
     */
    public function redeemGift($token) {
        $this->_cart_mapper->reset();
        $opg_mapper = new Model_Mapper_OrderProductGifts; 
        $gift = $opg_mapper->getUnredeemedByToken($token);
        if (!$gift) {
            $this->_message = 'Gift not found';
            return false;
        }
        $gift->product->cost = 0;
        if (!$this->_cart_mapper->addProduct($gift->product, false, $gift->id)) {
            $this->_message = 'An error ocurred while processing your gift';
            return false;
        }
        return true;
    }

    /**
     * @return Form_Cart
     * 
     */
    public function getCartForm() {
        $cart = $this->_cart_mapper->get();
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
        $cart = $this->_cart_mapper->get();
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
        $form_data = array_merge(
            $cart->billing->toArray(),
            $cart->shipping->toArray(),
            array('use_shipping' => $cart->use_shipping)
        );
        if (!$cart->isFreeOrder()) {
            $form_data = array_merge($form_data, $cart->payment->toArray());
        }
        // If user is logged in, use that data to populate form, otherwise
        // show saved data if any
        if ($users_svc->isAuthenticated() && $users_svc->getUser() &&
            $users_svc->getProfile()) {
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
        $cart = $this->_cart_mapper->get();
        $this->_cart_mapper->setBilling($form->billing->getValues(true));
        $this->_cart_mapper->setShipping($form->getShippingValues());
        $this->_cart_mapper->setUser($form->user->getValues(true));
        $this->_cart_mapper->setUserInfo($form->info->getValues(true));
        if (!$cart->isFreeOrder()) {
            $this->_cart_mapper->setPayment($form->payment->getValues(true));
        }
        $promo_code = $form->promo->promo_code->getValue();
        $existing_promo_code = ($cart->promo ? $cart->promo->code : '');
        if ($promo_code && $promo_code != $existing_promo_code) {
            $promos_mapper = new Model_Mapper_Promos;
            $promo = $promos_mapper->getByCode($promo_code);
            if ($promo) {
                $this->_cart_mapper->addPromo($promo);
            }
        } elseif (!strlen(trim($promo_code)) && $existing_promo_code) {
            $this->_cart_mapper->removePromo();
        }
        $use_shipping = (isset($data['use_shipping']) ?
            (int) $data['use_shipping'] : 0);
        $this->_cart_mapper->setUseShipping($use_shipping);
    }
    
    /**
     * @param Form_Checkout $form
     * @return bool
     * 
     */
    public function validateSavedForm(Form_Checkout $form) {
        $data = array_merge(
            $form->billing->getValues(true),
            $form->user->getValues(true),
            $form->getShippingValues(),
            array('promo_code' => $form->promo->promo_code->getValue())
        );
        if (!$this->_cart_mapper->get()->isFreeOrder()) {
            $data = array_merge($data, $form->payment->getValues(true));
        }
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
        $gateway_logger = new Model_Mapper_PaymentGateway_Logger_Orders;
        $config    = Zend_Registry::get('app_config');
        $logger    = Zend_Registry::get('log');
        // Operate on a copy of cart -- we don't want to modify it in here
        $cart      = clone $this->_cart_mapper->get();
        // Merge input data into one array
        $data = array_merge(
            $form->billing->getValues(true),
            $cart->getTotals(),
            $form->user->getValues(true),
            $form->getShippingValues(),
            $form->info->getValues(true)
        );
        $data['promo_id'] = ($cart->promo ? $cart->promo->id : null);
        $data['products'] = $cart->products->toArray();
        if (!$this->_cart_mapper->get()->isFreeOrder()) {
            $data = array_merge($data, $form->payment->getValues(true));
        }
        $order  = new Model_Cart_Order($data);
        $status = true;
        $db     = Zend_Db_Table::getDefaultAdapter();
        try {
            // Regular sale
            if (!$cart->isFreeOrder() && $config['use_payment_gateway']) {
                if ($cart->payment->payment_method == 'credit_card') {
                    $this->_gateway->processSale($order);
                } else {
                    $this->_gateway->processECSale(
                        $order, $cart->ec_token, $payer_id);
                }
            }
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            // Save user data
            $users_mapper = new Model_Mapper_Users;
            $profile_mapper = new Model_Mapper_UserProfiles;
            if ($users_svc->isAuthenticated()) {
                $order->user_id = $users_svc->getId();
                $users_mapper->updateEmail($order->email, $order->user_id);
                $profile_mapper->updateByUserId($order->toArray(),
                    $order->user_id);
            } else {
                // This inserts into users and user_profiles
                $order->password = $users_svc->generateHash($order->password);
                $order->user_id = $users_mapper->insert($order->toArray(), true);
                $profile_mapper->insert($order->toArray());
                if (!$order->user_id) {
                    throw new Exception('user_id not defined');
                }
            }
            // Save order data
            $orders_mapper = new Model_Mapper_Orders;
            $order->order_id = $orders_mapper->insert($order->toArray());
            $this->saveOrderProducts($order);
            if (!$cart->isFreeOrder()) {
                $order_payment_id = $this->saveOrderPayments($order);
            }
            $gateway_logger->insert(
                true,
                $order->toArray(),
                $this->_gateway->getRawCalls()
            );
            $db->commit();
        } catch (Exception $e) {
            $status = false;
            // These should fail silently if they do fail
            try {
                $this->_gateway->voidCalls();
                // Log
                $gateway_logger->insert(
                    $status,
                    $order->toArray(),
                    $this->_gateway->getRawCalls(),
                    array($e->getMessage() . ' -- ' . $e->getTraceAsString())
                );
            } catch (Exception $e1) {}
        }
        // Reset cart
        if ($status) {
            $this->_cart_mapper->setConfirmation($this->_cart_mapper->get());
            if ($config['reset_cart_after_process']) {
                $this->_cart_mapper->reset();
            }
        }
        return $status;

    }
    
    /**
     * @param Model_Cart_Order $order
     * @return void
     * 
     */
    public function saveOrderProducts(Model_Cart_Order $order) {
        $users_svc  = new Service_Users;
        $is_auth    = $users_svc->isAuthenticated();
        $user_id    = $users_svc->getId();
        $cart       = clone $this->_cart_mapper->get();
        $op         = new Model_Mapper_OrderProducts;
        $ops        = new Model_Mapper_OrderProductSubscriptions;
        $gifts      = new Model_Mapper_OrderProductGifts;
        $fmt        = 'Y-m-d H:i:s'; 
        $extra_days = ($cart->promo && $cart->promo->extra_days ?
                       $cart->promo->extra_days : 0);
        if ($is_auth) {
            $expirations = $users_svc->getExpirations();
        }
        foreach ($cart->products as $product) {
            // Insert into order_products
            $opid = $op->insert($product->toArray(), $order->order_id); 
            // Gift processing here
            if ($product->isGift()) {
                $token_generator = new TokenGenerator;
                $gifts->insert(array(
                    'order_product_id' => $opid,
                    'token'            => $token_generator->generate()
                ));
                // Gifts need no further processing
                continue;
            } elseif ($product->isRedeemedGift()) {
                // This is a gift that is being redeemed. Continue on to
                // process subscriptions, if any
                $gifts->redeem($opid, $product->order_product_gift_id);
            }
            if ($product->isSubscription()) {
                $expiration = null;
                // See if we need to renew
                if (isset($expirations->regular) && $expirations->regular) {
                    $expiration = $expirations->regular;
                }
                $term = (int) $product->term_months;
                // If expiration is null here, DateTime defaults to today
                $date = new DateTime($expiration);
                // Adjust from today
                $date->add(new DateInterval("P{$term}M{$extra_days}D"));
                $ops->insert(array(
                    'user_id'            => $order->user_id,
                    'order_product_id'   => $opid,
                    'expiration'         => $date->format($fmt)
                ));
            } elseif ($product->isDigital()) {
                $expiration = null;
                // See if we need to renew
                if (isset($expirations->digital) && $expirations->digital) {
                    $expiration = $expirations->digital;
                }
                $term = (int) $product->term_months;
                // If expiration is null here, DateTime defaults to today
                $date = new DateTime($expiration);
                // Adjust from today
                $date->add(new DateInterval("P{$term}M{$extra_days}D"));
                $ops->insert(array(
                    'user_id'            => $order->user_id,
                    'order_product_id'   => $opid,
                    'expiration'         => $date->format($fmt),
                    'digital_only'       => 1
                ));
            }
        }
    }

    /**
     * @param Model_Cart_Order
     * @return null|int The last insert id into OrderPayments
     * 
     */
    public function saveOrderPayments(Model_Cart_Order $order) {
        $gateway_responses = $this->_gateway->getSuccessfulResponseObjects();
        $payments_mapper = new Model_Mapper_OrderPayments;
        foreach ($gateway_responses as $response) {
            $payment_data = array(
                'order_id'        => $order->order_id,
                'amount'          => $order->total,
                'pnref'           => $response->pnref,
                'date'            => date('Y-m-d H:i:s')
            );
            if (is_a($response, 'Model_PaymentGateway_Response_Payflow')) {
                $payments_mapper->insert(array_merge($payment_data, array(
                    'payment_type_id'     => Model_PaymentType::PAYFLOW,
                    'cc_number'           => substr($order->cc_num, -4),
                    'cc_expiration_month' => $order->cc_exp_month,
                    'cc_expiration_year'  => $order->cc_exp_year,
                    'cvv2match'           => $response->cvv2match,
                    'ppref'               => $response->ppref,
                    'correlationid'       => $response->correlationid
                )));
            } elseif (is_a($response, 'Model_PaymentGateway_Response_Paypal')) {
                $payments_mapper->insert(array_merge($payment_data, array(
                    'correlationid'       => $response->correlationid,
                    'payment_type_id'     => Model_PaymentType::PAYPAL,
                    // Billing agreement id, for reference transactions
                    'baid'                => $response->baid
                )));
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
        $config = Zend_Registry::get('app_config');
        $gateway_logger = new Model_Mapper_PaymentGateway_Logger_ExpressCheckout;
        $cart = $this->_cart_mapper->get();
        $totals = $cart->getTotals();
        $data = array(
            'email'        => $cart->user->email,
            'total'        => $totals['total'],
            'return_url'   => $return_url,
            'cancel_url'   => $cancel_url,
            'products'     => $cart->products->toArray()
        );
        $order = new Model_Cart_Order($data);
        $status = true;
        try {
            $token = $this->_gateway->getECToken($order, $return_url, $cancel_url);
            if (!$token) {
                throw new Exception(__FUNCTION__ . '() failed');
            }
            $cart->ec_token = $token;
            $ot_mapper->insert(
                $status,
                $cart->toArray(),
                $this->_gateway->getRawCalls()
            );
        } catch (Exception $e) {
            $status = false;
            try {
                $ot_mapper->insert(
                    $status,
                    $cart->toArray(),
                    $this->_gateway->getRawCalls(),
                    array($e->getMessage() . ' ' . $e->getTraceAsString())
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
        return $this->_cart_mapper->getConfirmation();
    }

    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
    
}
