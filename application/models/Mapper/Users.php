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
     * @return int Num rows updated
     * 
     */
    public function updateLastLogin($id) {
        return $this->_users->update(array(
            'last_login' => date('Y-m-d G:i:s', time())), $id);
    }
    
    /**
     * Passwords are stored as sha1$salt$hash
     * 
     * @param string $hash
     * @param string $pw
     * 
     */
    public function validatePassword($hash, $value) {
        $pw = explode('$', $hash);
        if (count($pw) == 3) {
            $hash = sha1($pw[1] . $value);
            if ($hash == $pw[2]) {
                return true;
            }
        }
        return false;
    }
}

