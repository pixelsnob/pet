<?php
/**
 * Passwords are stored as sha1$salt$hash
 * 
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
        // Initialize return values
        $code = Zend_Auth_Result::FAILURE;
        $identity = null;
        $messages = array();

        $users = new Model_DbTable_Users;
        $user = $users->getByUsername($this->_username);
        if ($user) {
            $pw = preg_match('/^sha1\$([^\$]*)\$([^\$]*)$/',
                trim($user->password), $m);
            if ($pw && count($m) == 3) {
                $hash = sha1($m[1] . $this->_password);
                if ($hash == $m[2]) {
                    $code = Zend_Auth_Result::SUCCESS;
                    unset($user->password);
                    $identity = $user;
                }
            }
        }
        if (!$identity) {
            $messages[] = 'Auth error';
        }
        return new Zend_Auth_Result($code, $identity, $messages);
    }
}

