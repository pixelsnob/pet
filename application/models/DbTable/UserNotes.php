<?php
/**
 * @package Model_DbTable_UserNotes
 * 
 */
class Model_DbTable_UserNotes extends Zend_Db_Table_Abstract {

    protected $_name = 'user_notes';

    /**
     * @param int $user_id
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByUserId($user_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('un' => 'user_notes'), array(
                'un.*',
                "concat(u.first_name, ' ' , u.last_name) as user_name",
                "concat(au.first_name, ' ' , au.last_name) as rep_user_name"
            ))
            ->joinLeft(array('u' => 'users'), 'u.id = un.user_id', null)
            ->joinLeft(array('au' => 'users'), 'au.id = un.rep_user_id', null)
            ->where('un.user_id = ?', $user_id)
            ->order('un.date_created');
        return $this->fetchAll($sel);
    }
}

