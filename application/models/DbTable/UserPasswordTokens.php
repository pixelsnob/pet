<?php
/**
 * @package Model_DbTable_UserPasswordTokens
 * 
 */
class Model_DbTable_UserPasswordTokens extends Zend_Db_Table_Abstract {

    /**
     * @var string 
     * 
     */
    protected $_name = 'user_password_tokens';
    
    /**
     * @param int $user_id
     * @return Num rows deleted 
     * 
     */
    public function deleteByUserId($user_id) {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
        return $this->delete($where);
    }
}

