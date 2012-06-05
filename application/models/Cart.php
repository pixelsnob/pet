<?php
/**
 * Cart model
 * 
 */
class Model_Cart extends Pet_Model_Abstract implements Serializable {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'products'            => null,  // Model_Cart_Products
        'billing'             => null,  // Model_Cart_Billing
        'shipping'            => null,  // Model_Cart_Shipping
        'payment'             => null,  // Model_Cart_Payment
        'promo'               => null,  // Model_Promo
        'user'                => null,  // Model_Cart_User
        'user_info'           => null,  // Model_Cart_UserInfo
        'timestamp'           => null,  // Unix timestamp of last update
        'use_shipping'        => false, // Whether to show shipping subform
        'ec_token'            => null   // Express Checkout token
    );
    
    /**
     * @var Model_Cart_Validator_Abstract
     * 
     */
    protected $_validator = 'Model_Cart_Validator_Default';

    /**
     * @var string
     * 
     */
    protected $_message = '';

    /**
     * Set defaults
     * 
     * @return void
     * 
     */
    public function __construct() {
        $this->_data['products'] = new Model_Cart_Products;
        $this->_data['billing'] = new Model_Cart_Billing;
        $this->_data['shipping'] = new Model_Cart_Shipping;
        $this->_data['payment'] = new Model_Cart_Payment;
        $this->_data['user'] = new Model_Cart_User;
        $this->_data['user_info'] = new Model_Cart_UserInfo;
        $this->updateTimestamp();
    }
    
    /**
     * @return string
     * 
     */
    public function serialize() {
        return serialize($this->_data);
    }
    
    /**
     * @param $data string
     * @return void
     * 
     */
    public function unserialize($data) {
        $this->_data = unserialize($data);
    }

    /**
     * @return Model_Cart_Validator_Abstract
     * 
     */
    public function getValidator() {
        $validator = new $this->_validator;
        $validator->setCart($this);
        return $validator;
    }
    
    /**
     * @return bool
     * 
     */
    public function validate() {
        $validator = $this->getValidator();
        if (!$validator->validate()) {
            $this->_message = $validator->getMessage();
            return false;
        }
        return true;
    }

    /**
     * @param Model_Cart_Product $product
     * @return bool
     * 
     */
    public function addProduct(Model_Cart_Product $product) {
        $product->key = $product->product_id . ($product->is_gift ?
            'GIFT' : '');
        $validator = $this->getValidator();
        if (!$validator->validateProduct($product)) {
            $this->_message = $validator->getMessage();
            return false;
        }
        if ($this->_data['products']->getByKey($product->key)) {
            $this->_data['products']->incrementQty($product->key);
        } else {
            $this->_data['products']->add($product);
        }
        return true;
    }
    
    /**
     * @param int $product_id
     * @param bool $is_gift
     * @return void
     * 
     */
    public function removeProduct($product_id, $is_gift = false) {
        $key = $product_id . ($is_gift ? 'GIFT' : ''); 
        if ($product = $this->_data['products']->getByKey($key)) {
            $msg = '"' . $product->name . '" was removed from your cart';
            $this->_data['products']->remove($key);
            if ($this->_data['promo']) {
                $valid = $this->getValidator()->validatePromo($this->_data['promo']); 
                if (!$valid) {
                    $this->_message = $this->getValidator()->getMessage();
                    $this->removePromo();
                }
            }
        }
    }

    /** 
     * @param string $key
     * @param int $qty
     * @return bool
     */
    public function setProductQty($key, $qty) {
        if ($product = $this->_data['products']->getByKey($key)) {
            if ($qty) {
                $this->_data['products']->setQty($key, $qty);
            } else {
                $this->_data['products']->remove($product_id);
            }
            return true;
        }
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function update(array $data) {
        foreach ($this->_data['products'] as $product) {
            if (isset($data['qty'][$product->key])) {
                $qty = (int) $data['qty'][$product->key];
                $this->setProductQty($product->key, $qty);
            }
            
        }
    }
    
    /**
     * @param Model_Cart_Billing
     * @return void
     * 
     */
    public function setBilling(Model_Cart_Billing $billing) {
        $this->_data['billing'] = $billing;
    }

    /**
     * @param Model_Cart_Shipping
     * @return void
     * 
     */
    public function setShipping(Model_Cart_Shipping $shipping) {
        $this->_data['shipping'] = $shipping;
    }

    /**
     * @param Model_Cart_User
     * @return void
     * 
     */
    public function setUser(Model_Cart_User $user) {
        $this->_data['user'] = $user;
    }

    /**
     * @param Model_Cart_UserInfo
     * @return void
     * 
     */
    public function setUserInfo(Model_Cart_UserInfo $user_info) {
        $this->_data['user_info'] = $user_info;
    }

    /**
     * @param Model_Cart_UserInfo
     * @return void
     * 
     */
    public function setPayment(Model_Cart_Payment $payment) {
        $this->_data['payment'] = $payment;
    }

    /**
     * @param bool $use_shipping
     * @return void
     * 
     */
    public function setUseShipping($use_shipping) {
        $this->_data['use_shipping'] = (int) $use_shipping;
    }

    /**
     * @param int $product_id
     * @return void
     * 
     */
    /*public function incrementProductQty($product_id) {
        $this->_data['products']->incrementQty($product_id);
    }*/

    /**
     * Returns number of products in the cart
     * 
     * @return int
     */
    public function getQty() {
        return count($this->_data['products']);
    }

    /**
     * @param int $product_type_id
     * @param bool $is_gift
     * @return int
     * 
     */
    public function getQtyByProductTypeId($product_type_id, $is_gift = false) {
        $qty = 0;
        foreach ($this->_data['products'] as $product) {
            if ($product->product_type_id == $product_type_id) {
                if (!$is_gift && $product->isGift()) {
                    continue; 
                }
                $qty += $product->qty;
            }
        }
        return $qty;
    }

    /**
     * @return array
     * 
     */
    public function getShippingValues() {
        if ($this->isShippingAddressRequired() && !$this->use_shipping) {
            $billing = $this->_data['billing'];
            return array(
                'shipping_first_name'  => $this->_data['user']['first_name'],
                'shipping_last_name'   => $this->_data['user']['last_name'],
                'shipping_address'     => $billing->billing_address,
                'shipping_address_2'   => $billing->billing_address_2,
                'shipping_company'     => $billing->billing_company,
                'shipping_city'        => $billing->billing_city,
                'shipping_state'       => $billing->billing_state,
                'shipping_postal_code' => $billing->billing_postal_code,
                'shipping_country'     => $billing->billing_country,
                'shipping_phone'       => $billing->billing_phone
            );
        } else {
            return $this->_data['shipping']->toArray();
        }
    }

    /**
     * @return bool
     * 
     */
    public function isShippingAddressRequired() {
        return $this->_data['products']->hasSubscription() ||
            $this->_data['products']->hasPhysical(); 
    }

    /**
     * Removes all renewals in the cart 
     * 
     * @return void 
     */
    public function removeRenewals() {
        return $this->_data['products']->removeRenewals();
    }

    /**
     * @return array An array of totals
     * 
     */
    public function getTotals() {
        $totals['subtotal'] = 0;
        $totals['total'] = 0;
        $totals = array(
            'subtotal' => 0,
            'discount' => 0,
            'total'    => 0
        );
        foreach ($this->_data['products'] as $product) {
            $totals['subtotal'] += ($product->qty * $product->cost);
        }
        $totals['total'] = $totals['subtotal'];
        $promo = $this->_data['promo'];
        if ($totals['total'] && $promo && isset($promo->discount)) {
            $totals['discount'] = $promo->discount;
            $totals['total'] = $totals['total'] - $totals['discount'];
        }
        return $totals;
    }
    
    /**
     * @param Model_Promo $promo
     * @return bool
     * 
     */
    public function addPromo(Model_Promo $promo) {
        if (!$this->getValidator()->validatePromo($promo)) {
            $this->_message = $this->getValidator()->getMessage();
            return false;
        }
        $this->_data['promo'] = $promo;
        return true;
    }
    
    /**
     * @return void
     * 
     */
    public function removePromo() {
        $code = $this->_data['promo']->code;
        $this->_data['promo'] = null;
    }

    /**
     * Sets timestamp to current time
     * 
     * @return void 
     */
    public function updateTimestamp() {
        $this->_data['timestamp'] = time();
    }
    
    /**
     * @return array 
     */
    public function toArray() {
        $products = array();
        foreach ($this->_data['products'] as $product) {
            $products[] = $product->toArray();
        }
        $data = $this->_data;
        $data = array_merge($this->_data, array(
            'products'  => $products,
            'billing'   => $this->_data['billing']->toArray(),
            'shipping'  => $this->_data['shipping']->toArray(),
            'payment'   => $this->_data['payment']->toArray(),
            'user'      => $this->_data['user']->toArray(),
            'user_info' => $this->_data['user_info']->toArray()
        ));
        return $data;
    }
    
    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
}
