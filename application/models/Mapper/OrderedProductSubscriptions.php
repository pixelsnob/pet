<?php
/**
 * @package Model_Mapper_OrderedProductSubscriptions
 * 
 */
class Model_Mapper_OrderedProductSubscriptions extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_op_subs = new Model_DbTable_OrderedProductSubscriptions;
    }

    /**
     * @param int $user_id
     * @return Zend_DbTable_Row_Abstract 
     * 
     */
    public function getUnexpiredByUserId($user_id) {
        return $this->_op_subs->getUnexpiredByUserId($user_id); 
    }
}

