<?php

class Pet_Validate_PasswordStrength extends Zend_Validate_Abstract {
    
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';
 
    protected $_messageTemplates = array(
        self::LENGTH => "Password must be at least 8 characters in length",
        self::UPPER  => "Password must contain at least one uppercase letter",
        self::LOWER  => "Password must contain at least one lowercase letter",
        self::DIGIT  => "Password must contain at least one digit character"
    );
 
    public function isValid($value) {
        
        $this->_setValue($value);
 
        if (strlen($value) < 8) {
            $this->_error(self::LENGTH);
            return false;
        }
        /*if (!preg_match('/[A-Z]/', $value)) {
            $this->_error(self::UPPER);
            return false;
        }*/
        if (!preg_match('/[a-z]/', $value)) {
            $this->_error(self::LOWER);
            return false;
        }
        if (!preg_match('/\d/', $value)) {
            $this->_error(self::DIGIT);
            return false;
        }
        return true;
    }
}