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
     * @param bool $for_update
     * @return Zend_DbTable_Row_Abstract 
     * 
     */
    public function getUnexpiredByUserId($user_id, $digital_only = null,
                                         $for_update = false) {
        $ops = $this->_ops->getUnexpiredByUserId($user_id, $digital_only,
                   $for_update); 
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

}

