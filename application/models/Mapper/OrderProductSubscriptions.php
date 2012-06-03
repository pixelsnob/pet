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

