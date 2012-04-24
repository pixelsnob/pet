<?php
/**
 * @package Model_Cart_User
 * 
 */
class Model_Cart_User extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'first_name'          => '',
        'last_name'           => '',
        'username'            => '',
        'email'               => '',
        'password'            => ''
    );
}
