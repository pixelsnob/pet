<?php
/**
 * @package Pet_Validate_Currency
 * 
 */
class Pet_Validate_Currency extends Zend_Validate_Abstract {
    
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
        self::INVALID => 'Amount is invalid'
    );

    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $valid = preg_match('/^\d+(\.\d\d)?$/', $value);
        if (!$valid) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
