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
                case Model_Product::PRODUCT_TYPE_DOWNLOAD;
                    $dl = $this->_products->getDownloadByProductId($id);
                    if ($dl) {
                        $data = array_merge($product->toArray(),
                            $dl->toArray());
                        return new Model_Product_Download($data);
                    }
                    break;
                case Model_Product::PRODUCT_TYPE_PHYSICAL;
                    $physical = $this->_products
                        ->getPhysicalProductByProductId($id);
                    if ($physical) {
                        $data = array_merge($product->toArray(),
                            $physical->toArray());
                        return new Model_Product_Physical($data);
                    }
                    break;
                case Model_Product::PRODUCT_TYPE_COURSE;
                    $course = $this->_products->getCourseByProductId($id);
                    if ($course) {
                        $data = array_merge($product->toArray(),
                            $course->toArray());
                        return new Model_Product_Course($data);
                    }
                    break;
                case Model_Product::PRODUCT_TYPE_SUBSCRIPTION;
                    $mapper = new Model_Mapper_Subscriptions; 
                    $sub = $this->_products->getSubscriptionByProductId($id);
                    if ($sub) {
                        $data = array_merge($product->toArray(),
                            $sub->toArray());
                        return new Model_Product_Subscription($data);
                    }
                    break;

            }
        }
    }

}
