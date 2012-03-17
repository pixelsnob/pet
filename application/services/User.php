<?php
/**
 * User service layer
 *
 * @package Service_User
 * 
 */
class Service_User {
    
    /**
     * @return void
     * 
     */
    public function __construct() {}

    /**
     * @param $data Username and password, etc.
     * @return bool Auth status
     * 
     * 
     */
    public function authenticate($data) {
        $username = (isset($data['username']) ? $data['username'] : '');
        $password = (isset($data['password']) ? $data['password'] : '');
        $auth_adapter = new Pet_Auth_Adapter($username, $password);
        $auth = Zend_Auth::getInstance();
        return $auth->authenticate($auth_adapter)->isValid();
    }
    
    /**
     * @return void
     * 
     */
    public function logout() {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * @return bool Auth status
     * 
     */
    public function isAuthenticated() {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    /*public function hasAccess($resource_name) {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return false;
        }
        $identity = $auth->getIdentity();
        $acl = Zend_Registry::get('acl');
        return $acl->isAllowed($identity->user_role, $resource_name);
    }*/
}
