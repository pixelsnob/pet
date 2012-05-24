<?php
/**
 * @package Model_Mapper_OrderedProducts
 * 
 */
class Model_Mapper_OrderedProducts_DigitalSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_op_digital_subs = new Model_DbTable_OrderedProducts_DigitalSubscriptions;
    }

    /**
     * @param int $user_id
     * @return Zend_DbTable_Row_Abstract 
     */
    public function getUnexpiredByUserId($user_id) {
        return $this->_op_digital_subs->getUnexpiredByUserId($user_id); 
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_model = new Model_OrderedProduct_DigitalSubscription($data);
        $this->_op_digital_subs->insert($op_model->toArray());
    }

}

