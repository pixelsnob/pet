<?php

class Pet_Validate_PasswordStrength extends Zend_Validate_Abstract {
    
    const LENGTH  = 'length';
    const LETTER  = 'letter';
    const DIGIT   = 'digit';
    const SPECIAL = 'special';
    const CHARS = 'chars';
 
    protected $_messageTemplates = array(
        self::LENGTH  => 'Password must be at least 8 characters in length',
        self::LETTER  => 'Password must contain at least one letter',
        self::DIGIT   => 'Password must contain at least one number',
        self::SPECIAL => 'Password must contain a letter, a number, and at least one of ! @ $ % ^ & * ( ) + ? _ -',
        self::SPECIAL => 'Password must contain at least one of ! @ $ % ^ & * ( ) + ? _ -'
    );
 
    public function isValid($value) {
        if (strlen($value) < 8) {
            $this->_error(self::LENGTH);
            return false;
        }
        if (preg_match('/[^a-z1-9!@\$%\^&\*\(\)\+\?_\-]/i', $value)) {
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
        if (!preg_match('/[!@\$%\^&\*\(\)\+\?_\-]/', $value)) {
            $this->_error(self::SPECIAL);
            return false;
        }
        return true;
    }
}
