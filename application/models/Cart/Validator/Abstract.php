<?php
/**
 * @package Model_Cart_Validator
 * 
 * 
 */
abstract class Model_Cart_Validator_Abstract {
    
    /**
     * @param Model_Cart
     *
     */
    protected $_cart;
    
    /**
     * @param Model_Cart_Product $product
     * 
     */
    abstract public function isProductValid(Model_Cart_Product $product);
    
    /**
     * @param Model_Cart $cart
     * @return void 
     * 
     */
    public function setCart(Model_Cart $cart) {
        $this->_cart = $cart;
    }
}
