<?php

class Model_DbTable_Users extends Zend_Db_Table_Abstract {

    protected $_name = 'users';

    public function getByUsername($username) {
        $sel = $this->select()->where('username = ?', $username);
        return $this->fetchRow($sel);
    }

    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }

    public function update($data, $id) {
        unset($data['id']);
        parent::update($data, $this->getAdapter()->quoteInto('id = ?', $id));
    }
}
