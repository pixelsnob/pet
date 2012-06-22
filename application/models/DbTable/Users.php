<?php

class Model_DbTable_Users extends Zend_Db_Table_Abstract {
    
    /**
     * @var string 
     * 
     */
    protected $_name = 'users';
    
    /**
     * @param int $id
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }
    
    /**
     * @param string $username 
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getByUsername($username) {
        $sel = $this->select()->where('username = ?', $username);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $username 
     * @param bool $is_superuser
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getActiveByUsername($username, $is_superuser = false) {
        $is_superuser = (int) $is_superuser;
        $sel = $this->select()->where('username = ?', $username)
            ->where('is_active = 1')
            ->where('is_superuser = ?', $is_superuser);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $email
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getActiveByEmail($email) {
        $sel = $this->select()->where('email = ?', $email)
            ->where('is_active = 1');
        return $this->fetchRow($sel);
    }


    /**
     * @param string $email
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getByEmail($email) {
        $sel = $this->select()->where('email = ?', $email);
        return $this->fetchRow($sel);
    }

    /** 
     * @param array $data
     * @param int $id
     * @return int Num rows updated
     * 
     */
    public function update(array $data, $id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return parent::update($data, $where);
    }
}
