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
        'timestamp'           => null // Unix timestamp of last update
    );
    
    protected $_validator;

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
        $this->updateTimestamp();
    }
    
    /**
     * @param Model_Cart_Validator_Abstract $validator
     * @return void
     * 
     */
    public function setValidator(Model_Cart_Validator_Abstract $validator) {
        $this->_validator = $validator;
        $this->_validator->setCart($this);
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
     * @param Model_Cart_Product $product
     * @return void
     * 
     */
    public function addProduct(Model_Cart_Product $product) {
        print_r($this->_validator); 
        if ($this->_validator && !$this->_validator->isProductValid($product)) {
            throw new Exception('Error adding product');
        }
        $this->_data['products'][$product->product_id] = $product;
    }
    
    /**
     * @param int $product_id
     * @return void
     * 
     */
    public function removeProduct($product_id) {
        if (in_array($product_id, array_keys($this->_data['products']))) {
            unset($this->_data['products'][$product_id]);
        }
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
     * @param int $product_type_id
     * @return int
     */
    public function getQtyByProductTypeId($product_type_id) {
        $qty = 0;
        foreach ($this->_data['products'] as $product) {
            if ($product->product_type_id == $product_type_id) {
                $qty += $product->qty;
            }
        }
        return $qty;
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
     * @return bool
     * 
     */
    public function hasSubscription() {
        return (bool) $this->getQtyByProductTypeId(
            Model_ProductType::SUBSCRIPTION);
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
