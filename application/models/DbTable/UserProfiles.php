<?php

class Model_DbTable_UserProfiles extends Zend_Db_Table_Abstract {

    protected $_name = 'user_profiles';

    public function getByUserId($id) {
        $sel = $this->select()
            ->where('user_id = ?', $id);
        return $this->fetchRow($sel);
    }

    public function updateByUserId($data, $user_id) {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
        return parent::update($data, $where);
    }
}
