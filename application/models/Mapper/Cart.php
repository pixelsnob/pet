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
    
    public function addProduct(Model_Product $product) {
        $product = new Model_Cart_Product($product->toArray());
        $this->_cart->products->add($product); 
    }

    public function removeProduct($product_id) {
        $this->_cart->products->remove($product_id); 
    }
    
    public function setProductQty($product_id, $qty) {
        $this->_cart->products->setQty($product_id, $qty); 
    }
    
    public function init() {
        $session = new Zend_Session_Namespace;
        if (!isset($session->cart)) {
            $session->cart = new Model_Cart;
        }
        $this->_cart = $session->cart;
    }

    public function reset() {
        $session = new Zend_Session_Namespace;
        $session->cart = new Model_Cart;
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

