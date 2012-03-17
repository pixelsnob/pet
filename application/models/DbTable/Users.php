<?php

class Model_DbTable_Users extends Zend_Db_Table_Abstract {

    protected $_name = 'users';

    public function getByUsername($username) {
        $stmt = $this->select()->where('username = ?', $username);
        return $this->fetchRow($stmt);
    }
}
