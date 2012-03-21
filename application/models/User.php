<?php
/**
 * @package Model_User
 * 
 */
class Model_User extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'username' => null,
        'first_name' => null,
        'last_name' => null,
        'email' => null,
        'password' => null,
        'is_staff' => null,
        'is_active' => null,
        'is_superuser' => null,
        'last_login' => null,
        'date_joined' => null
    );

}

