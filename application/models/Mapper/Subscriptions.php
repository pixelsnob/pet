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
        $sub_model = new Model_Subscription($data);
        $sub = $sub_model->toArray();
        unset($sub['id']);
        unset($sub['product_id']);
        $this->_subscriptions->updateByProductId($sub, $product_id); 
    }

    /**
     * @param array $data
     * @return int product_id
     * 
     */
    function insert(array $data) {
        $sub = new Model_Subscription($data);
        $sub_array = $sub->toArray();
        unset($sub_array['id']);
        print_r($sub_array);
        return $this->_subscriptions->insert($sub_array);
    }
}

