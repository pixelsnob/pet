<?php
/**
 * @package Model_Mapper_OrderProductSubscriptions
 * 
 */
class Model_Mapper_OrderProductSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_ops = new Model_DbTable_OrderProductSubscriptions;
    }
    
    /**
     * @param int $order_id
     * @return array
     * 
     */
    public function getByOrderId($order_id) {
        $subs = $this->_ops->getByOrderId($order_id);
        $products_mapper = new Model_Mapper_Products;
        $subs_array = array();
        if ($subs) {
            foreach ($subs as $sub) {
                $temp_sub = new Model_OrderProductSubscription(
                    $sub->toArray());
                $temp_product = $products_mapper->getById($sub->product_id);
                if (!$temp_product) {
                    $msg = "Product by id {$sub->product_id} not found";
                    throw new Exception($msg);
                }
                $temp_sub->product = $temp_product;
                $subs_array[] = $temp_sub;
            }
            return $subs_array;
        }
    }

    /**
     * @param int $user_id
     * @param mixed $digital_only
     * @return Zend_DbTable_Row_Abstract 
     * 
     */
    public function getUnexpiredByUserId($user_id, $digital_only = null) {
        $ops = $this->_ops->getUnexpiredByUserId($user_id, $digital_only); 
        if ($ops) {
            $ops_model = new Model_OrderProductSubscription($ops->toArray());
            return $ops_model;
        }
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $ops_model = new Model_OrderProductSubscription($data);
        $this->_ops->insert($ops_model->toArray());
    }
    
    /**
     * @param DateTime $expiration
     * @return array An array of Model_OrderProductSubscription objects
     * 
     */
    public function getByExpiration(DateTime $expiration) {
        $products_mapper = new Model_Mapper_Products;
        $subs = $this->_ops->getByExpiration($expiration->format('Y-m-d'));
        $subs_array = array();
        if ($subs) {
            foreach ($subs as $sub) {
                $ops_model = new Model_OrderProductSubscription($sub);
                $ops_model->min_expiration = $sub['min_expiration'];
                if ($sub['product_id']) {
                    $product = $products_mapper->getById($sub['product_id']);
                    $ops_model->product = $product;
                }
                $subs_array[] = $ops_model;
            }
        }
        return $subs_array;
    }

}

