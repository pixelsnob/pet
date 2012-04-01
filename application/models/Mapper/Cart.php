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
    
    public function init() {
        $session = new Zend_Session_Namespace;
        if (!isset($session->cart)) {
            $session->cart = new Model_Cart;
        }
        $this->_cart = $session->cart;
    }
    
    public function get() {
        return $this->_cart;
    }

    
    public function addProduct($product) {
        $product = new Model_Cart_Product(array('product' => $product));
        return $this->_cart->addProduct($product); 
    }

    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id); 
    }
    
    public function setProductQty($product_id, $qty) {
        $this->_cart->setProductQty($product_id, $qty); 
    }

    public function reset() {
        $session = new Zend_Session_Namespace;
        $session->cart = new Model_Cart;
        $this->_cart = $session->cart;
    }
    
    public function setValidator(Model_Cart_Validator_Abstract $validator) {
        $this->_cart->setValidator($validator);
    }

    public function getMessage() {
        return $this->_cart->getMessage();
    }

    public function addPromo(Model_Promo $promo) {
        return $this->_cart->addPromo($promo);
    }

    public function removePromo() {
        $this->_cart->removePromo($promo);
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

