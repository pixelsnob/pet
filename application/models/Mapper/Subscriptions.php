<?php
/**
 * @package Model_Mapper_Subscriptions
 * 
 */
class Model_Mapper_Subscriptions extends Pet_Model_Mapper_Abstract {

    public function __construct() {
        $this->_subscriptions = new Model_DbTable_Subscriptions;
    }
    
    public function getByProductId($product_id) {
        $subscription = $this->_subscriptions->getByProductId($product_id);
        if ($subscription) {
            return new Model_Subscription($subscription->toArray());
        }
    }
}

