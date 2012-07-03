<?php
/**
 * @package Model_Mapper_DigitalSubscriptions
 * 
 */
class Model_Mapper_DigitalSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void 
     * 
     */
    public function __construct() {
        $this->_digital_subscriptions = new Model_DbTable_DigitalSubscriptions;
    }
    
    /**
     * @param array $data
     * @param int $product_id
     * @return void
     * 
     */
    public function updateByProductId($data, $product_id) {
        $digital_model = new Model_DigitalSubscription($data);
        $sub = $digital_model->toArray();
        unset($sub['id']);
        unset($sub['product_id']);
        $this->_digital_subscriptions->updateByProductId($sub, $product_id); 
    }

}

