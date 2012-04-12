<?php
/**
 * @package Model_Mapper_Cart
 * 
 */
class Model_Mapper_Cart extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->init();
        $this->_checkTimestamp();
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
        $session->cart->setValidator('Model_Cart_Validator_Default');
        $session->cart->getValidator()->validate();
        $this->_cart = $session->cart;
    
    }
    
    /**
     * @return Model_Cart
     * 
     */
    public function get() {
        return $this->_cart;
    }
    
    /**
     * @param Model_Product_Abstract $product
     * @return bool
     * 
     */

    public function addProduct(Model_Product_Abstract $product) {
        $product = new Model_Cart_Product(array('product' => $product));
        return $this->_cart->addProduct($product); 
    }

    /**
     * @param int $product_id
     * @return void
     * 
     */

    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id); 
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
     * @return void
     * 
     */
    public function reset() {
        $session = new Zend_Session_Namespace;
        $session->cart = new Model_Cart;
        $this->_cart = $session->cart;
    }
    
    public function update(array $data) {
        foreach ($this->_cart->products as $product) {
            $qty = (int) $data['qty'][$product->product_id];
            $this->_cart->setProductQty($product->product_id, $qty);
        }
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
     * @param Model_Promo $promo
     * @return bool
     * 
     */
    public function addPromo(Model_Promo $promo) {
        return $this->_cart->addPromo($promo);
    }

    /**
     * @return void
     * 
     */
    public function removePromo() {
        $this->_cart->removePromo($promo);
    }

    public function getTotals() {
        return $this->_cart->getTotals();
    }

    /**
     * Checks the timestamp, to make sure the cart hasn't timed out
     * 
     * @return void 
     */
    private function _checkTimestamp() {
        if (time() - $this->_cart->timestamp > 1800) {
            $this->reset();
        } else {
            $this->_cart->timestamp = time();
        }
    }
    
}

