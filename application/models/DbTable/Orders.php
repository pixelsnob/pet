<?php
/**
 * @package Model_DbTable_Orders
 * 
 */
class Model_DbTable_Orders extends Zend_Db_Table_Abstract {

    protected $_name = 'orders';
    
    /**
     * @param bool $email_sent
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByEmailSent($email_sent) {
        $sel = $this->select()->where('email_sent = ?', (int) $email_sent);
        return $this->fetchAll($sel);
    }

    /**
     * @param bool $user_id
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByUserId($user_id) {
        $sel = $this->select()->where('user_id = ?', $user_id);
        return $this->fetchAll($sel);
    }


}

