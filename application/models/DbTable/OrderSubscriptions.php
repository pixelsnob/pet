<?php
/**
 * @package Model_DbTable_OrderSubscriptions
 * 
 */
class Model_DbTable_OrderSubscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'order_subscriptions';

    /**
     * @param int $user_id
     * @param bool $for_update
     * @return Zend_DbTable_Row  
     */
    public function getUnexpiredByUserId($user_id, $for_update = false) {
        $sel = $this->select()
            ->where('expiration >= ?', date('Y-m-d'))
            ->where('user_id = ?', $user_id)
            ->order(array('expiration desc'));
        if ($for_update) {
            $sel->forUpdate();
        }
        return $this->fetchRow($sel);
    }

}

