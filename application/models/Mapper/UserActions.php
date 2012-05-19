<?php
/**
 * @package Model_Mapper_UserActions
 * 
 * For logging user activity
 * 
 */
class Model_Mapper_UserActions extends Pet_Model_Mapper_Abstract {
    
    /**
     * @param string $action Description of user action
     * @param string $ip
     * @param int $user_id
     * @return void
     * 
     */
    public function insert($action, $ip, $user_id) {
        $mongo = Pet_Mongo::getInstance(); 
        $user_action = new Model_UserAction;
        $user_action->user_id = $user_id;
        $user_action->action = $action;
        $user_action->ip = $ip;
        $user_action->timestamp = date('Y-m-d H:i:s');
        $user_action_array = $user_action->toArray();
        unset($user_action_array['id']);
        $mongo->user_actions->insert($user_action_array);
    }
}

