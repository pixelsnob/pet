<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
class Service_Cart {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        //$this->_users = new Model_Mapper_Users;
        $this->_cart = new Model_Mapper_Cart;
        $this->_products = new Model_Mapper_Products;
    }

    public function addProduct($product_id) {
        $this->_cart->addProduct($product_id);
    }
    
    public function removeProduct($product_id) {

    }

    public function incrementProductQty($product_id) {

    }
}
