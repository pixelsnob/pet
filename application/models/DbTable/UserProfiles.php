<?php

class Model_DbTable_UserProfiles extends Zend_Db_Table_Abstract {

    protected $_name = 'user_profiles';

    public function getByUserId($id) {
        $sel = $this->select()
            ->where('user_id = ?', $id);
        return $this->fetchRow($sel);
    }

}
