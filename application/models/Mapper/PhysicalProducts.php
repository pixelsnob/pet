<?php
/**
 * @package Model_Mapper_PhysicalProducts
 * 
 */
class Model_Mapper_PhysicalProducts extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_physical_products = new Model_DbTable_PhysicalProducts;
    }
    
    /**
     * @param array $data
     * @param int $product_id
     * @return void
     * 
     */
    public function updateByProductId($data, $product_id) {
        $physical_model = new Model_PhysicalProduct($data);
        $product = $physical_model->toArray();
        unset($product['id']);
        unset($product['product_id']);
        unset($product['shipping_id']); // ask about this
        $this->_physical_products->updateByProductId($product, $product_id);
    }

}

