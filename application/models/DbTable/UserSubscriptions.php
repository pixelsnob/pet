<?php
/**
 * @package Model_DbTable_UserSubscriptions
 * 
 */
class Model_DbTable_UserSubscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'user_subscriptions';
    
    public function getByUserId($user_id) {
        $sel = $this->select()->where('user_id = ?', $user_id)
            ->order('expiration desc')
            ->limit(1);
        return $this->fetchRow($sel);
    }

}

