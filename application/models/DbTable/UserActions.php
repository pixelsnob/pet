<?php
/**
 * @package Model_DbTable_UserActions
 * 
 */
class Model_DbTable_UserActions extends Zend_Db_Table_Abstract {

    protected $_name = 'user_actions';

    /**
     * @param int $user_id
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByUserId($user_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ua' => 'user_actions'), array(
                'ua.*',
                "concat(u.first_name, ' ' , u.last_name) as user_name",
                "concat(au.first_name, ' ' , au.last_name) as rep_user_name"
            ))
            ->joinLeft(array('u' => 'users'), 'u.id = ua.user_id', null)
            ->joinLeft(array('au' => 'users'), 'au.id = ua.rep_user_id', null)
            ->where('ua.user_id = ?', $user_id);
        return $this->fetchAll($sel);
    }
}

