<?php
/**
 * @package Model_Mapper_OrderSubscriptions
 * 
 */
class Model_Mapper_OrderSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_os = new Model_DbTable_OrderSubscriptions;
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
        return $this->_os->getUnexpiredByUserId($user_id, $digital_only,
                   $for_update); 
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_model = new Model_OrderSubscription($data);
        $this->_os->insert($op_model->toArray());
    }

}

