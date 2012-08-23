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
     * @return array
     * 
     */
    public function getAll() {
        $products = $this->_products->fetchAll($this->_products->select());
        if ($products) {
            $out = array();
            foreach ($products as $product) {
                $out[] = $this->getById($product->id, false);
            }
            return $out;
        }
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
            return $this->getItem($product, $is_active_check);
        }
    }

    /**
     * @param array $ids
     * @param bool $is_active_check Whether to check if the product is active
     * @return Model_Product_Abstract
     * 
     */
    public function getByIds(array $ids, $is_active_check = true) { 
        $out = array();
        foreach ($ids as $id) {
            $db_product = $this->_products->getById($id);
            if ($db_product) {
                $product = new Model_Product($db_product->toArray());
                $out[] = $this->getItem($product, $is_active_check);
            }
        }
        return $out;
    }
    
    /**
     * @return array|null
     * 
     */
    public function getNamesGroupedByProductType() {
        $product_types = $this->getProductTypes();
        if ($product_types) {
            $out = array();
            foreach ($product_types as $product_type) {
                $ptid = $product_type->id;
                $products = $this->getByProductType($ptid);
                $products_array = array();
                foreach ($products as $product) {
                    $products_array[$product->product_id] = $product->name;
                }
                asort($products_array);
                $out[$product_type->plural_name] = $products_array;
            }
            return $out;
        }
    }
    
    /**
     * @return array|null
     * 
     */
    public function getByProductType($product_type) {
        $products = $this->_products->getByProductType($product_type);
        if ($products) {
            $out = array();
            foreach ($products as $product) {
                $product_model = new Model_Product($product->toArray());
                $out[] = $this->getItem($product_model, false);
                
            }
            return $out;
        }
    }

    /**
     * @param int $term
     * @param int $zone
     * 
     */
    public function getSubscriptionByTermAndZone($term, $zone) {
        $product = $this->_products->getSubscriptionByTermAndZone($term, $zone);
        if ($product) {
            return new Model_Product_Subscription($product->toArray());
        }
    }

    /**
     * @param Model_Product $product
     * @param bool $is_active_check
     * @return Model_Product_Abstract 
     * 
     */
    public function getItem(Model_Product $product, $is_active_check = true) {
        $id = $product->id;
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
    
    /**
     * @param string $sku
     * @return Model_Product|null
     * 
     */
    public function getBySku($sku) {
        $db_product = $this->_products->getBySku($sku);
        if ($db_product) {
            $product = new Model_Product($db_product->toArray());
            return $this->getItem($product);
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
     * @param array $params
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sel = $db->select()->from('view_products');
        if (isset($params['product_type']) && (int) $params['product_type']) {
            $sel->where('product_type_id = ?', (int) $params['product_type']);
        }
        $sel->order(array('product_type asc', 'name asc'));
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
        $products = array();
        foreach ($paginator as $row) {
            $product_model = new Model_Product($row);
            $item = $this->getItem($product_model, false);
            if ($item) {
                $product_model->item = $item;
            }
            $products[] = $product_model;
        }
        return array('paginator' => $paginator, 'data' => $products);
    }

    /**
     * @return array
     * 
     */
    public function getProductTypes() {
        $pt = new Model_DbTable_ProductTypes;
        $product_types = $pt->fetchAll($pt->select()->order('name'));
        $out = array();
        if ($product_types) {
            foreach ($product_types as $product_type) {
                $out[] = new Model_ProductType($product_type->toArray());
            }
        }
        return $out;
    }

    /**
     * @param array $data
     * @param int $id
     * @return void
     * 
     */
    public function update(array $data, $id) {
        $product_model = new Model_Product($data);
        $product = $product_model->toArray();
        unset($product['id']);
        $this->_products->update($product, $id); 
        $digital_mapper       = new Model_Mapper_DigitalSubscriptions;
        $subscription_mapper  = new Model_Mapper_Subscriptions;
        $physical_mapper      = new Model_Mapper_PhysicalProducts;
        $course_mapper        = new Model_Mapper_Courses;
        $download_mapper      = new Model_Mapper_Downloads;
        switch ($product['product_type_id']) {
            case Model_ProductType::SUBSCRIPTION:
                $subscription_mapper->updateByProductId($data, $id);
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                $digital_mapper->updateByProductId($data, $id);
                break;
            case Model_ProductType::PHYSICAL:
                $physical_mapper->updateByProductId($data, $id);
                break;
            case Model_ProductType::COURSE:
                $course_mapper->updateByProductId($data, $id);
                break;
            case Model_ProductType::DOWNLOAD:
                $download_mapper->updateByProductId($data, $id);
                break;

        }
    }

    /**
     * @param array $data
     * @return int $product_id
     * 
     */
    public function insert(array $data) {
        $product_model        = new Model_Product($data);
        $product              = $product_model->toArray();
        $data['product_id']   = $this->_products->insert($product); 
        $digital_mapper       = new Model_Mapper_DigitalSubscriptions;
        $subscription_mapper  = new Model_Mapper_Subscriptions;
        $physical_mapper      = new Model_Mapper_PhysicalProducts;
        $course_mapper        = new Model_Mapper_Courses;
        $download_mapper      = new Model_Mapper_Downloads;
        switch ($product['product_type_id']) {
            case Model_ProductType::SUBSCRIPTION:
                $subscription_mapper->insert($data);
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                $digital_mapper->insert($data);
                break;
            case Model_ProductType::PHYSICAL:
                $physical_mapper->insert($data);
                break;
            case Model_ProductType::COURSE:
                $course_mapper->insert($data);
                break;
            case Model_ProductType::DOWNLOAD:
                $download_mapper->insert($data);
                break;

        }
        return $data['product_id'];
    }

    /**
     * @param int $id
     * @return void
     * 
     */
    public function delete($id) {
        $where = $this->_products->getAdapter()->quoteInto('id = ?', $id);
        $this->_products->delete($where);
    }
}
