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
        'products'            => null, // Model_Cart_Products
        'billing'             => null, // Model_Cart_Billing
        'shipping'            => null, // Model_Cart_Shipping
        'payment'             => null,
        'promo'               => null, // Model_Promo
        'user'                => null, // Model_Cart_User
        'user_info'           => null, // Model_Cart_UserInfo
        'timestamp'           => null  // Unix timestamp of last update
    );
    
    /**
     * @var Model_Cart_Validator_Abstract
     * 
     */
    protected $_validator = 'Model_Cart_Validator_Default';

    /**
     * Set defaults
     * 
     * @return void
     * 
     */
    public function __construct() {
        $this->_data['products'] = array();
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
     * @param Model_Cart_Product $product
     * @return bool
     * 
     */
    public function addProduct(Model_Cart_Product $product) {
        if (!$this->getValidator()->validateProduct($product)) {
            return false;
        }
        if (isset($this->_data['products'][$product->product_id])) {
            $this->_data['products'][$product->product_id]->qty++;
        } else {
            $this->_data['products'][$product->product_id] = $product;
        }
        $messenger = Zend_Registry::get('messenger');
        $msg = '"' . $product->name . '" was added to your cart';
        $messenger->setNamespace('cart')->addMessage($msg);
        return true;
    }
    
    /**
     * @param int $product_id
     * @return void
     * 
     */
    public function removeProduct($product_id) {
        if (in_array($product_id, array_keys($this->_data['products']))) {
            $msg = '"' . $this->_data['products'][$product_id]->name .
                '" was removed from your cart';
            unset($this->_data['products'][$product_id]);
            $messenger = Zend_Registry::get('messenger');
            $messenger->setNamespace('cart')->addMessage($msg);
            if ($this->_data['promo']) {
                $valid = $this->getValidator()
                    ->validatePromo($this->_data['promo'], false); 
                if (!$valid) {
                    $this->removePromo();
                }
            }
        }
    }

    /** 
     * @param int $product_id
     * @param int $qty
     * @return bool
     */
    public function setProductQty($product_id, $qty) {
        if (isset($this->_data['products'][$product_id])) {
            if ($qty) {
                $this->_data['products'][$product_id]->qty = $qty;
            } else {
                $this->removeProduct($product_id);
            }
            return true;
        }
        return false;
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function update(array $data) {
        foreach ($this->_data['products'] as $product) {
            if (isset($data['qty'][$product->product_id])) {
                $qty = (int) $data['qty'][$product->product_id];
                $this->setProductQty($product->product_id, $qty);
            }
        }
        $messenger = Zend_Registry::get('messenger');
        $messenger->setNamespace('cart')->addMessage('Cart updated');
    }
    
    /**
     * @param Model_Cart_Billing
     * @return void
     * 
     */
    public function saveBilling(Model_Cart_Billing $billing) {
        $this->_data['billing'] = $billing;
    }

    /**
     * @param Model_Cart_Shipping
     * @return void
     * 
     */
    public function saveShipping(Model_Cart_Shipping $shipping) {
        $this->_data['shipping'] = $shipping;
    }

    /**
     * @param Model_Cart_User
     * @return void
     * 
     */
    public function saveUser(Model_Cart_User $user) {
        $this->_data['user'] = $user;
    }

    /**
     * @param Model_Cart_UserInfo
     * @return void
     * 
     */
    public function saveUserInfo(Model_Cart_UserInfo $user_info) {
        $this->_data['user_info'] = $user_info;
    }

    /**
     * @param int $product_id
     * @return void
     * 
     */
    public function incrementProductQty($product_id) {
        if (isset($this->_data['products'][$product_id])) {
            $this->_data['products'][$product_id]->qty++;
        }
    }

    /**
     * Returns number of products in the cart
     * 
     * @return int
     */
    public function getQty() {
        $qty = 0;
        foreach ($this->products as $product) {
            $qty += $product->qty;
        }
        return $qty;
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
                if (!$is_gift && $product->is_gift) {
                    continue; 
                }
                $qty += $product->qty;
            }
        }
        return $qty;
    }

    /**
     * @param string $product_id
     * @return Model_Cart_Prod|false
     * 
     */
    public function getProduct($product_id) {
        if (!array_key_exists($product_id, $this->_data['products'])) {
            return false;
        }
        return $this->_data['products'][$product_id];
    }

    /**
     * @return array
     * 
     */
    public function getProductIds() {
        return array_keys($this->_data['products']);
    }

    /**
     * @return bool
     * 
     */
    public function hasProducts() {
        return (bool) count($this->_data['products']);
    }
    
    
    /**
     * @return bool
     * 
     */
    public function hasDownload() {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::SUBSCRIPTION);
    }
    
    /**
     * @return bool
     * 
     */
    public function hasPhysical() {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::PHYSICAL);
    }

    /**
     * @return bool
     * 
     */
    public function hasCourse() {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::COURSE);
    }
    
    /**
     * @param bool $is_gift
     * @return bool
     * 
     */
    public function hasSubscription($is_gift = false) {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::SUBSCRIPTION, $is_gift);
    }

    /**
     * @return bool
     * 
     */
    public function hasDigitalSubscription() {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::DIGITAL_SUBSCRIPTION);
    }
    
    /**
     * @return bool
     * 
     */
    public function hasRenewal() {
        $c = 0;
        foreach ($this->_data['products'] as $product) {
            if ($product->is_renewal) {
                $c++;
            }
        }
        return (bool) $c;
    }
    
    /**
     * @return bool
     * 
     */
    public function isShippingAddressRequired() {
        return $this->hasSubscription() || $this->hasPhysical(); 
    }

    /**
     * Removes all renewals in the cart 
     * 
     * @return void 
     */
    public function removeRenewals() {
        $c = 0;
        foreach ($this->_data['products'] as $product) {
            if ($product->is_renewal) {
                $this->removeProduct($product->product_id);
            }
        }
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
        if ($promo && isset($promo->discount)) {
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
            return false;
        }
        $this->_data['promo'] = $promo;
        $messenger = Zend_Registry::get('messenger');
        $messenger->setNamespace('cart')
            ->addMessage('Promo "' . $promo->code . '" added');
        return true;
    }
    
    /**
     * @return void
     * 
     */
    public function removePromo() {
        $code = $this->_data['promo']->code;
        $this->_data['promo'] = null;
        $messenger = Zend_Registry::get('messenger');
        $messenger->setNamespace('cart')
            ->addMessage('Promo "' . $code . '" removed');
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
            'products'    => $products,
            'billing'  => $this->_data['billing']->toArray(),
            'shipping' => $this->_data['shipping']->toArray(),
            'payment'  => $this->_data['payment']->toArray()
        ));
        return $data;
    }
    
}
