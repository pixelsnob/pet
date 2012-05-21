<?php
/**
 * @package Model_Mapper_OrderedProductDigitalSubscriptions
 * 
 */
class Model_Mapper_OrderedProductDigitalSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_op_digital_subs = new Model_DbTable_OrderedProductDigitalSubscriptions;
    }

    /**
     * @param int $user_id
     * @return Zend_DbTable_Row_Abstract 
     */
    public function getUnexpiredByUserId($user_id) {
        return $this->_op_digital_subs->getUnexpiredByUserId($user_id); 
    }

}

