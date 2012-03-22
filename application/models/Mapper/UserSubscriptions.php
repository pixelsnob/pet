<?php
/**
 * @package Model_Mapper_UserSubscriptions
 * 
 */
class Model_Mapper_UserSubscriptions extends Pet_Model_Mapper_Abstract {
    
    /**
     * @param int $user_id
     * @return void|Model_UserSubscription
     * 
     */
    public function getByUserId($user_id) {
        $user_subs = new Model_DbTable_UserSubscriptions;
        $sub = $user_subs->getByUserId($user_id);
        if ($sub) {
            return new Model_UserSubscription($sub->toArray());
        }
    }
}

