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
        $this->_physical_products->updateByProductId($product, $product_id);
    }

    /**
     * @param array $data
     * @return int product_id
     * 
     */
    function insert(array $data) {
        $product = new Model_PhysicalProduct($data);
        $product_array = $product->toArray();
        unset($product_array['id']);
        return $this->_physical_products->insert($product_array);
    }

}

