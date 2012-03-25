<?php
/**
 * @package Pet_Validate_StoredPassword
 * 
 */
class Pet_Validate_StoredPassword extends Zend_Validate_Abstract {
    
    /**
     * Message constants
     * 
     */
    const INVALID = 'invalid';
    
    /**
     * @var array $_messageTemplates
     * 
     */
    protected $_messageTemplates = array(
        self::INVALID => 'Password does not match'
    );
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $users_svc = new Service_Users;
        if (!$users_svc->validatePassword($identity->password, $value)) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
