<?php
/**
 * Cart model
 * 
 */
class Model_Cart extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'products'            => null, // Model_Cart_Products
        'billing'             => null, // Model_Cart_Billing
        'shipping'            => null, // Model_Cart_Shipping
        'payment'             => null,
        'timestamp'           => null, // Unix timestamp of last update
        /*'coupon_code'         => '',
        'user_id'             => 0,
        'order_id'            => 0*/
    );
    
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
        $this->updateTimestamp();
    }
    
    /**
     * Makes sure that billing is set to an instance of Model_Cart_BillInfo
     * 
     * @param Model_Cart_BillInfo $billing
     * @return void
     */
    public function setBilling(Model_Cart_Billing $billing) {
        return $billing;
    }
    
    /**
     * Makes sure that shipping is set to an instance of Model_Cart_Shipping
     * 
     * @param Model_Cart_Shipping $shipping
     * @return void
     */
    public function setShipping(Model_Cart_Shipping $shipping) {
        return $shipping;
    }
    
    /**
     * @param string $id
     * @return Model_Cart_Prod|false
     * 
     */
    public function getProduct($id) {
        if (!array_key_exists($id, $this->_data['products'])) {
            return false;
        }
        return $this->_data['products'][$key];
    }

    /**
     * @param int $key
     * @return void
     * 
     */
    public function incrementProductQty($key) {
        if (isset($this->_data['products'][$key])) {
            $this->_data['products'][$key]->qty++;
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
     * @param int $key
     * @param int $qty
     * @return bool
     */
    public function setProductQty($key, $qty) {
        if (isset($this->_data['products'][$key])) {
            $this->_data['products'][$key]->qty = $qty;
            return true;
        }
        return false;
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
    
    /**
     * Clone properties that are objects
     * 
     * @return void
     */
    /*public function __clone() {
        $this->_data['billing'] = clone $this->_data['billing'];
        $this->_data['shipping'] = clone $this->_data['shipping'];
        $this->_data['totals'] = clone $this->_data['totals'];
        $this->_data['payment'] = clone $this->_data['payment'];
        $products = array();
        foreach ($this->_data['products'] as $k => $prod) {
            $products[$k] = clone $prod;
        }
        $this->_data['products'] = $products;
    }*/
}
