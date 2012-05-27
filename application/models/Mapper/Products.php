<?php
/**
 * @package Model_Mapper_Products
 * 
 */
class Model_Mapper_Products extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void 
     * 
     */
    public function __construct() {
        $this->_products = new Model_DbTable_Products;
    }
    
    /**
     * @param int $id
     * @return Model_Product_Abstract
     * 
     */
    public function getById($id) {
        $db_product = $this->_products->getById($id);
        if ($db_product) {
            $product = new Model_Product($db_product->toArray());
            switch ($product->product_type_id) {
                case Model_ProductType::DOWNLOAD;
                    $dl = $this->_products->getDownloadByProductId($id);
                    if ($dl) {
                        $data = array_merge($product->toArray(),
                            $dl->toArray());
                        return new Model_Product_Download($data);
                    }
                    break;
                case Model_ProductType::PHYSICAL;
                    $physical = $this->_products
                        ->getPhysicalProductByProductId($id);
                    if ($physical) {
                        $data = array_merge($product->toArray(),
                            $physical->toArray());
                        return new Model_Product_Physical($data);
                    }
                    break;
                case Model_ProductType::COURSE;
                    $course = $this->_products->getCourseByProductId($id);
                    if ($course) {
                        $data = array_merge($product->toArray(),
                            $course->toArray());
                        return new Model_Product_Course($data);
                    }
                    break;
                case Model_ProductType::SUBSCRIPTION;
                    $sub = $this->_products->getSubscriptionByProductId($id);
                    if ($sub) {
                        $data = array_merge($product->toArray(),
                            $sub->toArray());
                        $model = new Model_Product_Subscription($data);
                        $sz_mapper = new Model_Mapper_SubscriptionZones;
                        $sz = $sz_mapper->getById($model->zone_id);
                        if ($sz) {
                            $model->zone = $sz->name;
                        }
                        return $model;
                    }
                    break;
                case Model_ProductType::DIGITAL_SUBSCRIPTION;
                    $sub = $this->_products
                        ->getDigitalSubscriptionByProductId($id);
                    if ($sub) {
                        $data = array_merge($product->toArray(),
                            $sub->toArray());
                        return new Model_Product_DigitalSubscription($data);
                    }
                    break;

            }
        }
    }

    /**
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return array
     * 
     */
    public function getDigitalSubscriptions($is_giftable = null,
                                            $is_renewal = false) {
        $subs = $this->_products->getDigitalSubscriptions($is_giftable,
            $is_renewal);
        $out = array();
        foreach ($subs as $sub) {
            $out[] = new Model_Product_DigitalSubscription($sub->toArray());
        }
        return $out;
    }

    /**
     * @param int $zone_id
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return array
     * 
     */
    public function getSubscriptionsByZoneId($zone_id, $is_giftable = null,
                                             $is_renewal = false) {
        $subs = $this->_products->getSubscriptionsByZoneId($zone_id,
            $is_giftable, $is_renewal);
        $out = array();
        foreach ($subs as $sub) {
            $sub = new Model_Product_Subscription($sub->toArray());
            $out[] = $sub;
        }
        return $out;
    }

    /**
     * @param int $zone_id
     * @param bool $is_gift
     * @param bool $is_renewal
     * @return array
     * 
     */
    public function getPhysicalProducts() {
        $products = $this->_products->getPhysicalProducts();
        $out = array();
        foreach ($products as $product) {
            $out[] = new Model_Product_Physical($product->toArray());
        }
        return $out;
    }
}
