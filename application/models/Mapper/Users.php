<?php
/**
 * @package Model_Mapper_Users
 * 
 */
class Model_Mapper_Users extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_users = new Model_DbTable_Users;
    }
    
    /**
     * @param string $username
     * @return void|Model_User
     */
    public function getByUsername($username) {
        $user = $this->_users->getByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $username
     * @return void|Model_User
     */
    public function getActiveByUsername($username) {
        $user = $this->_users->getActiveByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $email
     * @return void|Model_User
     */
    public function getActiveByEmail($email) {
        $user = $this->_users->getActiveByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $email
     * @return void|Model_User
     * 
     */
    public function getByEmail($email) {
        $user = $this->_users->getByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param int $id
     * @return void|Model_User
     * 
     */
    public function getById($id) {
        $user = $this->_users->getById($id);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param array $data
     * @param int $id User id
     * @return int Num rows updated
     */
    public function updatePersonal(array $data, $id) {
        $user = new Model_User($data);
        $user_array = array(
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'username'   => $user->username,
            'email'      => $user->email
        );
        return $this->_users->update($user_array, $id);
    }

    /**
     * @param string $pw
     * @param int $id User id
     * @return int Num rows updated
     * 
     */
    public function updatePassword($pw, $id) {
        return $this->_users->update(array('password' => $pw), $id);
    }
    
    /**
     * @param int $id User id
     * @return int user_id 
     * 
     */
    public function updateLastLogin($id) {
        return $this->_users->update(array(
            'last_login' => date('Y-m-d H:i:s', time())), $id);
    }

    /**
     * @param array $data
     * @return int user_id
     * 
     */
    public function insert(array $data) {
        $user = new Model_User($data);
        $user->date_joined = date('Y-m-d H:i:s');
        $user_array = $user->toArray();
        unset($user_array['id']);
        return $this->_users->insert($user_array);
    }
}

