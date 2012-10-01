<?php
/**
 * @package Model_Mapper_Cart
 * 
 */
class Model_Mapper_Cart extends Pet_Model_Mapper_Abstract {
    
    /**
     * @var Model_Cart
     * 
     */
    protected $_cart;
    
    /**
     * @var Model_Cart_Confirmation
     * 
     */
    protected $_confirmation;
    
    /**
     * @var string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->init();
        $this->_checkTimestamp();
        $this->_checkConfirmationTimestamp();
    }
    
    /**
     * @return void
     * 
     */
    public function init() {
        $session = new Zend_Session_Namespace;
        if (!isset($session->cart)) {
            $session->cart = new Model_Cart;
        }
        /*if (!$session->cart->getValidator()->validate()) {
            throw new Exception('Validation failed');
        }*/
        $this->_cart = $session->cart;
        if (isset($session->cart_confirmation)) {
            $this->_confirmation = $session->cart_confirmation;
        }
    }
    
    /**
     * @return Model_Cart
     * 
     */
    public function get() {
        $session = new Zend_Session_Namespace;
        return $session->cart;
    }
    
    /**
     * @param int $product_id
     * @param bool $is_gift
     * @param null|int $order_product_gift_id The order_product_gift id, used
     *                                        to redeem a gift
     * @return bool
     * 
     */
    public function addProduct(Model_Product_Abstract $product,
                               $is_gift = false,
                               $order_product_gift_id = null) {
        $product = new Model_Cart_Product(array(
            'product'               => $product,
            'is_gift'               => $is_gift,
            'order_product_gift_id' => $order_product_gift_id
        ));
        $status = $this->_cart->addProduct($product);
        $this->_message = $this->_cart->getMessage();
        return $status;
    }

    /**
     * @param int $product_id
     * @param bool $is_gift
     * @param null|int $order_product_gift_id The order_product_gift id, used
     *                                        to redeem a gift
     * @return bool
     * 
     */
    public function addProductById($product_id, $is_gift = false,
                                   $order_product_gift_id = null) {
        $products_mapper = new Model_Mapper_Products;
        $sz_mapper = new Model_Mapper_ShippingZones;
        $product = $products_mapper->getById($product_id);
        if ($product) {
            $shipping_zone = null;
            if ($product->product_type_id == Model_ProductType::PHYSICAL) {
                $shipping_zone = $sz_mapper->getById($product->shipping_zone_id);
                if (!$shipping_zone) {
                    throw new Exception('Misconfigured/missing shipping zone');
                }
            }   
            $product_model = new Model_Cart_Product(array(
                'product'               => $product,
                'is_gift'               => $is_gift,
                'order_product_gift_id' => $order_product_gift_id,
                'shipping_zone'         => $shipping_zone
            ));
            if ($this->_cart->addProduct($product_model, $is_gift)) {
                return true;
            } else {
                $this->_message = $this->_cart->getMessage();
                return false;
            }
        }
        $this->_message = 'Product not found';
        return false;
    }

    /**
     * @param string $key
     * @return void
     * 
     */

    public function removeProduct($key) {
        $this->_cart->removeProduct($key); 
    }
    
    /**
     * @param string $key
     * @param int $qty
     * @return void
     * 
     */
    /*public function setProductQty($key) {
        $this->_cart->setProductQty($key); 
    }*/
    
    /**
     * @param Model_Cart $cart
     * @param Model_Cart_Order $order
     * 
     */
    public function setConfirmation(Model_Cart $cart, Model_Cart_Order $order) {
        $confirm = new Model_Cart_Confirmation;
        $confirm->cart = $cart;
        $confirm->order = $order;
        $confirm->timestamp = time();
        $session = new Zend_Session_Namespace;
        $session->cart_confirmation = $confirm;
        $this->_confirmation = $session->cart_confirmation;
    }
    
    /**
     * @return Model_Confirmation|void
     * 
     */
    public function getConfirmation() {
        $session = new Zend_Session_Namespace;
        if ($session->cart_confirmation) {
            return $session->cart_confirmation;
        }
    }

    /**
     * @return void
     * 
     */
    public function reset() {
        $session = new Zend_Session_Namespace;
        $session->cart = new Model_Cart;
        $this->_cart = $session->cart;
    }

    /**
     * @return void
     * 
     */
    public function resetConfirmation() {
        $session = new Zend_Session_Namespace;
        unset($session->cart_confirmation);
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
     * @param string $validator
     * @return void
     * 
     */
    public function setValidator($validator) {
        $this->_cart->setValidator($validator);
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function setBilling($data) {
        $billing = new Model_Cart_Billing($data);
        $this->_cart->setBilling($billing);
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function setShipping($data) {
        $shipping = new Model_Cart_Shipping($data);
        $this->_cart->setShipping($shipping);
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function setUser($data) {
        $users_svc = new Service_Users;
        $user = new Model_Cart_User($data);
        if (isset($data['password']) && strlen(trim($data['password']))) {
            $user->password_hash = $users_svc->generateHash($data['password']);
        }
        $this->_cart->setUser($user);
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function setUserInfo($data) {
        $user_info = new Model_Cart_UserInfo($data);
        $this->_cart->setUserInfo($user_info);
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function setPayment($data) {
        $payment = new Model_Cart_Payment($data);
        $payment->cc_num = '';
        $this->_cart->setPayment($payment);
    }

    /**
     * @param bool $use_shipping
     * @return void
     * 
     */
    public function setUseShipping($use_shipping) {
        $this->_cart->setUseShipping($use_shipping);
    }

    /**
     * @param Model_Promo $promo
     * @return bool
     * 
     */
    public function addPromo($code) {
        $cart = $this->get();
        if (!strlen(trim($code))) {
            if ($cart->promo) {
                $this->_message = 'Promo removed';
                $this->_cart->removePromo();
            }
            return true;
        }
        $promos_mapper = new Model_Mapper_Promos;
        $promo = $promos_mapper->getByCode($code);
        if ($promo && $this->_cart->addPromo($promo)) {
            $this->_message = "Promo $code added";
            return true;
        } else {
            $this->_message = 'Promo is not valid';
            return false;
        }
    }

    /**
     * @return string
     * 
     */
    public function removePromo() {
        return $this->_cart->removePromo();
    }
    
    /**
     * @return array
     * 
     */
    public function getTotals() {
        return $this->_cart->getTotals();
    }
    
    /**
     * @return array
     * 
     */
    public function getShippingValues() {
        return $this->cart->getShippingValues();
    }

    /**
     * @return void 
     * 
     */
    private function _checkTimestamp() {
        $config = Zend_Registry::get('app_config');
        $timeout = (int) $config['cart_timeout'];
        if (time() - $this->_cart->timestamp > $timeout) {
            $this->reset();
        } else {
            $this->_cart->timestamp = time();
        }
    }

    /**
     * @return void 
     * 
     */
    private function _checkConfirmationTimestamp() {
        if (!$this->_confirmation) {
            return;
        }
        $config = Zend_Registry::get('app_config');
        $timeout = (int) $config['confirmation_timeout'];
        if (time() - $this->_confirmation->timestamp > $timeout) {
            $this->resetConfirmation(); 
        } else {
            $this->_confirmation->timestamp = time();
        }
    }
    
    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
}

