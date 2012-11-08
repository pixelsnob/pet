<?php
/**
 * @package Pet_Validate_DateNotBeforeToday
 * 
 */
class Pet_Validate_DateNotBeforeToday extends Zend_Validate_Abstract {
    
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
        self::INVALID => "Date must be today's date or later"
    );
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        try {
            $date = new DateTime($value);
            $today = new DateTime;
            $today->setTime(0, 0, 0);
            if ($date->getTimestamp() < $today->getTimestamp()) {
                $this->_error(self::INVALID);
                return false;
            }
            return true;
        } catch (Exception $e) { 
            $this->_error(self::INVALID);
            return false;
        }
    }
}
