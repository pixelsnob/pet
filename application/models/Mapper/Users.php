<?php
/**
 * @package Model_Mapper_Users
 * 
 */
class Model_Mapper_Users extends Pet_Model_Mapper_Abstract {

    public function getByUsername($username) {
        $users = new Model_DbTable_Users;
        $user = $users->getByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    public function getByEmail($email) {
        $users = new Model_DbTable_Users;
        $user = $users->getByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }


    public function getById($id) {
        $users = new Model_DbTable_Users;
        $user = $users->getById($id);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    public function save($data, $id) {
        $users = new Model_DbTable_Users;
        $user = new Model_User($data);
        $users->update($user->toArray(), $id);
    }
}

