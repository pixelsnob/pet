<?php
/**
 * @package Model_Mapper_Products
 * 
 */
class Model_Mapper_Products extends Pet_Model_Mapper_Abstract {
    
    public function __construct() {
        $this->_products = new Model_DbTable_Products;
    }
    
    public function getById($id) {
        $product = $this->_products->getById($id);
        if ($product) {
            return new Model_Product($product->toArray());
        }
    }
}
