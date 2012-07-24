<?php
/**
 * @package Model_Mapper_UserActions
 * 
 * For logging user activity
 * 
 */
class Model_Mapper_UserActions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_user_actions = new Model_DbTable_UserActions;
    }
    
    /**
     * @param int $user_id
     * @return array
     * 
     */
    public function getByUserId($user_id) {
        $user_actions = $this->_user_actions->getByUserId($user_id); 
        $out = array();
        if ($user_actions) {
            foreach ($user_actions as $user_action) {
                $out[] = new Model_UserAction($user_action->toArray());
            }
        }
        return $out;
    }
}

