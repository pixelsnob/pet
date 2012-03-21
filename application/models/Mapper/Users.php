<?php
/**
 * @package Model_Mapper_Users
 * 
 */
class Model_Mapper_Users extends Pet_Model_Mapper_Abstract {
    
    public function __construct() {
        $this->_users = new Model_DbTable_Users;
    }
    
    /**
     * 
     * 
     */
    public function getByUsername($username) {
        $user = $this->_users->getByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function getByEmail($email) {
        $user = $this->_users->getByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function getById($id) {
        $user = $this->_users->getById($id);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function updatePersonal($data, $id) {
        $user = new Model_User($data);
        $user_array = array(
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'username'   => $user->username,
            'email'      => $user->email
        );
        return $this->_users->update($user_array, $id);
    }

    public function updateLastLogin($id) {
        return $this->_users->update(array('last_login' => date('Y-m-d G:i:s', time())), $id);
    }
}

