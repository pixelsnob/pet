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
        $this->reset();
        $this->_checkTimestamp();
    }
    
    public function addProduct(Model_Product $product) {
        
    }

    public function removeProduct($product_id) {
        
    }
    
    public function incrementProductQty($product_id) {
        
    }
    
    public function reset() {
        $session = new Zend_Session_Namespace;
        if (!isset($session->cart)) {
            $session->cart = new Model_Cart;
        }
        $this->_cart = $session->cart;
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

