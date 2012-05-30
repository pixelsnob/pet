<?php
/**
 * @package Model_DbTable_Orders
 * 
 */
class Model_DbTable_Orders extends Zend_Db_Table_Abstract {

    protected $_name = 'orders';
    
    /**
     * @param bool $email_sent
     * @param bool $for_update
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByEmailSent($email_sent, $for_update = false) {
        $sel = $this->select()
            ->where('email_sent = ?', (int) $email_sent);
        return $this->fetchAll($sel);
    }
}

