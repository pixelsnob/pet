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
                case Model_ProductType::DOWNLOAD;
                    $dl = $this->getDownloadByProductId($id);
                    if ($dl) {
                        $data = array_merge($product->toArray(),
                            $dl->toArray());
                        return new Model_Product_Download($data);
                    }
                    break;
                case Model_ProductType::PHYSICAL;
                    $physical = $this->getPhysicalProductByProductId($id);
                    if ($physical) {
                        $data = array_merge($product->toArray(),
                            $physical->toArray());
                        return new Model_Product_Physical($data);
                    }
                    break;
                case Model_ProductType::COURSE;
                    $course = $this->getCourseByProductId($id);
                    if ($course) {
                        $data = array_merge($product->toArray(),
                            $course->toArray());
                        return new Model_Product_Course($data);
                    }
                    break;
                case Model_ProductType::SUBSCRIPTION;
                    $sub = $this->getSubscriptionByProductId($id);
                    if ($sub) {
                        $data = array_merge($product->toArray(),
                            $sub->toArray());
                        return new Model_Product_Subscription($data);
                    }
                    break;
                case Model_ProductType::DIGITAL_SUBSCRIPTION;
                    $sub = $this->getDigitalSubscriptionByProductId($id);
                    if ($sub) {
                        $data = array_merge($product->toArray(),
                            $sub->toArray());
                        return new Model_Product_DigitalSubscription($data);
                    }
                    break;

            }
        }
    }

    public function getDownloadByProductId($product_id) {
        return $this->_products->getDownloadByProductId($product_id);
    }

    public function getPhysicalProductByProductId($product_id) {
        return $this->_products->getPhysicalProductByProductId($product_id);
    }
    
    public function getCourseByProductId($product_id) {
        return $this->_products->getCourseByProductId($product_id);
    }
    
    public function getSubscriptionByProductId($product_id) {
        return $this->_products->getSubscriptionByProductId($product_id);
    }

    public function getDigitalSubscriptions() {
        $subs = $this->_products->getDigitalSubscriptions();
        $out = array();
        foreach ($subs as $sub) {
            $out[] = new Model_Product_DigitalSubscription($sub->toArray());
        }
        return $out;
    }

    public function getDigitalSubscriptionByProductId($product_id) {
        return $this->_products->getDigitalSubscriptionByProductId($product_id);
    }

    public function getSubscriptionsByZoneId($zone_id, $is_gift = false, $is_renewal = null) {
        $subs = $this->_products->getSubscriptionsByZoneId($zone_id, $is_gift, $is_renewal);
        $out = array();
        foreach ($subs as $sub) {
            $sub = new Model_Product_Subscription($sub->toArray());
            $out[] = $sub;
        }
        return $out;
    }

}
