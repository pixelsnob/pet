<?php
/** 
 * @package Service_Products
 * 
 */
class Service_Products {
    
    /**
     * @param string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_products = new Model_Mapper_Products;
    }
    
    public function getById($product_id) {
        return $this->_products->getById($product_id);
    }

    public function getSubscriptionByProductId($product_id) {
        return $this->_products->getSubscriptionByProductId($product_id);
    }
}
