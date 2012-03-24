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
        $users = new Model_Mapper_Users;
        if (!$users->validatePassword($identity->password, $value)) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
