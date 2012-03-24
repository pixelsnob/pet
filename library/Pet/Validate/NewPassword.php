<?php
/**
 * @package Pet_Validate_NewPassword
 * 
 */
class Pet_Validate_NewPassword extends Zend_Validate_Abstract {
    
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
        self::INVALID => 'New password must be different than old password'
    );
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $pw = (isset($context['password']) ? $context['password'] : '');
        if (trim($pw) == trim($value)) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
