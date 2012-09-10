<?php
/**
 * Password strength validator
 * 
 */
class Pet_Validate_PasswordStrength extends Zend_Validate_Abstract {
    
    const LENGTH  = 'length';
    const LETTER  = 'letter';
    const DIGIT   = 'digit';
    const CHARS   = 'chars';
 
    /**
     * @var array
     * 
     */
    protected $_messageTemplates = array(
        self::LENGTH  => 'Password must be at least 6 characters in length',
        self::LETTER  => 'Password must contain at least one letter',
        self::DIGIT   => 'Password must contain at least one number',
        self::CHARS   => 'Password must contain only basic alphanumeric characters'
    );
    
    /**
     * @param string $value
     * @return bool
     * 
     */
    public function isValid($value) {
        if (strlen($value) < 6) {
            $this->_error(self::LENGTH);
            return false;
        }
        if (preg_match('/[^a-z0-9!@\$%\^&\*\(\)\+\?_\-]/i', $value)) {
            $this->_error(self::CHARS);
            return false;
        }
        if (!preg_match('/[a-z]/i', $value)) {
            $this->_error(self::LETTER);
            return false;
        }
        if (!preg_match('/\d/', $value)) {
            $this->_error(self::DIGIT);
            return false;
        }
        return true;
    }
}
