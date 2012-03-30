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
        $db_product = $this->_products->getById($id);
        if ($db_product) {
            $product = new Model_Product($db_product->toArray());
            switch ($product->product_type_id) {
                case Model_Product::PRODUCT_TYPE_SUBSCRIPTION;
                    $mapper = new Model_Mapper_Subscriptions; 
                    $subscription = $mapper->getByProductId($id);
                    if ($subscription) {
                        $data = array_merge($product->toArray(),
                            $subscription->toArray());
                        return new Model_Product_Subscription($data);
                    }
                    break;

            }
        }
    }
}
