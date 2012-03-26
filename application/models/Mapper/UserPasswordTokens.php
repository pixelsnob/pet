<?php
/**
 * @package Model_Mapper_UserPasswordTokens
 * 
 */
class Model_Mapper_UserPasswordTokens extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_pw_tokens = new Model_DbTable_UserPasswordTokens;
    }
    
    /**
     * @param string $token
     * @param int $max_age
     * @return void|Model_UserPasswordToken
     * 
     */
    public function getByToken($token) {
        return $this->_pw_tokens->getByToken($token);
    }

    /**
     * @param string $token
     * @param int $max_age
     * @return void|Model_UserPasswordToken
     * 
     */
    public function getByMaxAge($token, $max_age) {
        $max_age = time() - $max_age;
        $max_age = date('Y-m-d G:i:s', $max_age);
        return $this->_pw_tokens->getByMaxAge($token, $max_age);        
    }

    /**
     * @param string $user_id
     * @return Num rows deleted
     * 
     */
    public function deleteByUserId($user_id) {
        return $this->_pw_tokens->deleteByUserId($user_id);
    }
    
    /**
     * @param int $user_id
     * @param string $token
     * @return Inserted row's primary key
     * 
     */
    public function add($user_id, $token) {
        return $this->_pw_tokens->insert(array(
            'user_id'   => $user_id,
            'token'     => $token,
            'timestamp' => date('Y-m-d G:i:s', time())
        ));
    }
    
}

