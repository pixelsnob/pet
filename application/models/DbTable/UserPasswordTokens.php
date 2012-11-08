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
     * @param string $token
     * @return Num rows deleted 
     * 
     */
    public function getByToken($token) {
        $sel = $this->select()->where('token = ?', $token);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $token
     * @param int $max_age
     * @return Num rows deleted 
     * 
     */
    public function getByMaxAge($token, $max_age) {
        $sel = $this->select()->where('token = ?', $token)
            ->where('timestamp > ?', $max_age);
        return $this->fetchRow($sel);
    }
    
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

