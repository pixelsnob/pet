<?php
/**
 * @package Pet_Validate_Phone
 * 
 */
class Pet_Validate_Phone extends Zend_Validate_Abstract {
    
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
        self::INVALID => 'Phone number is not valid'
    );
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        if (preg_match('/[^0-9\- \(\)\+]/', $value)) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
