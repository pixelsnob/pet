<?php
/**
 * @package Model_Mapper_Users
 * 
 */
class Model_Mapper_Users extends Pet_Model_Mapper_Abstract {
    
    /**
     * 
     * 
     */
    public function getByUsername($username) {
        $users = new Model_DbTable_Users;
        $user = $users->getByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function getByEmail($email) {
        $users = new Model_DbTable_Users;
        $user = $users->getByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function getById($id) {
        $users = new Model_DbTable_Users;
        $user = $users->getById($id);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * 
     * 
     */
    public function updatePersonalInfo($data, $id) {
        $users = new Model_DbTable_Users;
        $user = new Model_User($data);
        $user_array = array(
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'username'   => $user->username,
            'email'      => $user->email
        );
        return $users->update($user_array, $id);
    }
}

