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
     * @var bool
     * 
     */
    protected $_is_superuser = false;

    /**
     * Class constructor
     *
     * The constructor sets the username and password
     *
     * @param string $username
     * @param string $password
     * @param bool $is_superuser
     */
    public function __construct($username, $password, $is_superuser = false) {
        $this->_username = trim($username);
        $this->_password = trim($password);
        $this->_is_superuser = $is_superuser;
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
        $user = $users_svc->getActiveUserByUsername($this->_username,
            $this->_is_superuser);
        if ($user && $users_svc->validatePassword($user->password,
            $this->_password)) {
            $code = Zend_Auth_Result::SUCCESS;
            unset($user->password);
            $identity = $user;
            $expirations = $users_svc->getExpirations($user->id);
            $session = new Zend_Session_Namespace('pet_expirations');
            $session->regular = $expirations->regular;
            $session->digital = $expirations->digital;
        }
            
        if (!$identity) {
            $messages[] = 'Auth error';
        }
        return new Zend_Auth_Result($code, $identity, $messages);
    }
}
