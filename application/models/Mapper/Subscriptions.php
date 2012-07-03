<?php
/**
 * @package Model_Mapper_Subscriptions
 * 
 */
class Model_Mapper_Subscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_subscriptions = new Model_DbTable_Subscriptions;
    }
    
    /**
     * @param int $product_id
     * @return null|Model_Subscription
     * 
     */
    public function getByProductId($product_id) {
        $subscription = $this->_subscriptions->getByProductId($product_id);
        if ($subscription) {
            return new Model_Subscription($subscription->toArray());
        }
    }

    /**
     * @param array $data
     * @param int $product_id
     * @return void
     * 
     */
    public function updateByProductId($data, $product_id) {
        $digital_model = new Model_Subscription($data);
        $sub = $digital_model->toArray();
        unset($sub['id']);
        $this->_subscriptions->updateByProductId($sub, $product_id); 
    }
}

