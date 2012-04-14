<?php
/**
 * Custom Auth_Adapter
 * 
 */
class Pet_Auth_Adapter implements Zend_Auth_Adapter_Interface {
    
    /**
     * @var string
     * 
     */
    protected $_username = null;

    /**
     * @var string
     * 
     */
    protected $_password = null;

    /**
     * Class constructor
     *
     * The constructor sets the username and password
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password) {
        $this->_username = trim($username);
        $this->_password = trim($password);
    }

    /**
     * Authenticate
     *
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $code = Zend_Auth_Result::FAILURE;
        $identity = null;
        $messages = array();
        $users_svc = new Service_Users;
        $user = $users_svc->getActiveUserByUsername($this->_username);
        if ($user && $users_svc->validatePassword($user->password,
            $this->_password)) {
            $code = Zend_Auth_Result::SUCCESS;
            unset($user->password);
            $identity = $user;
        }
            
        if (!$identity) {
            $messages[] = 'Auth error';
        }
        return new Zend_Auth_Result($code, $identity, $messages);
    }
}
