<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
class Service_Cart {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_users = new Model_Mapper_Users;
    }

}
