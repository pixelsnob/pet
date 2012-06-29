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
     * @param bool $is_active_check Whether to check if the product is active
     * @return Model_Product_Abstract
     * 
     */
    public function getById($id, $is_active_check = true) {
        $db_product = $this->_products->getById($id);
        if ($db_product) {
            $product = new Model_Product($db_product->toArray());
            switch ($product->product_type_id) {
                case Model_ProductType::DOWNLOAD;
                    $dl = $this->_products->getDownloadByProductId($id,
                        $is_active_check);
                    if ($dl) {
                        $data = array_merge($product->toArray(),
                            $dl->toArray());
                        return new Model_Product_Download($data);
                    }
                    break;
                case Model_ProductType::PHYSICAL;
                    $physical = $this->_products
                        ->getPhysicalProductByProductId($id, $is_active_check);
                    if ($physical) {
                        $data = array_merge($product->toArray(),
                            $physical->toArray());
                        return new Model_Product_Physical($data);
                    }
                    break;
                case Model_ProductType::COURSE;
                    $course = $this->_products->getCourseByProductId($id,
                        $is_active_check);
                    if ($course) {
                        $data = array_merge($product->toArray(),
                            $course->toArray());
                        return new Model_Product_Course($data);
                    }
                    break;
                case Model_ProductType::SUBSCRIPTION;
                    $sub = $this->_products->getSubscriptionByProductId($id,
                        $is_active_check);
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
                    $sub = $this->_products->getDigitalSubscriptionByProductId($id,
                        $is_active_check);
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
    public function getSubscriptions() {
        $subs = $this->_products->getSubscriptions();
        $out = array();
        foreach ($subs as $sub) {
            $out[] = new Model_Product_Subscription($sub->toArray());
        }
        return $out;
    }

    /**
     * @param string $country_name
     * @return Model_SubscriptionZone 
     * 
     */
    public function getSubscriptionZoneByName($country_name) {
        $sz_mapper = new Model_Mapper_SubscriptionZones;
        return $sz_mapper->getByName($country_name);
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

    /** 
     * Gets paginated products
     * 
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sel = $this->_products->select();
        $sel->order(array('id asc'));
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
        $products = array();
        foreach ($paginator as $row) {
            $product_model = new Model_Product($row);
            $temp_prod = $this->getById($row['id'], false);
            if ($temp_prod) {
                $product_model->item = $temp_prod;
            }
            $products[] = $product_model;
        }
        return array('paginator' => $paginator, 'data' => $products);
    }
}
