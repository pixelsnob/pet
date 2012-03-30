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
        $this->_cart = new Model_Mapper_Cart;
        $this->_products = new Model_Mapper_Products;
    }

    public function addProduct($product_id) {
        $product = $this->_products->getById($product_id);
        if ($product) {
            $this->_cart->addProduct($product);
        } else {
            $msg = "Product with product_id $product_id not found";
            throw new Exception($msg);
        }
    }
    
    public function setProductQty($product_id, $qty) {
        $this->_cart->setProductQty($product_id, $qty);
    }

    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id);
    }

    public function reset() {
        $this->_cart->reset();
    }
}
