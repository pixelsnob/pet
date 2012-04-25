<?php
/**
 * @package Pet_Validate_CCExpDate
 * 
 */
class Pet_Validate_CCExpDate extends Zend_Validate_Abstract {
    
    /**
     * Field name of country field to look for in $context in isValid()
     * 
     * @var string
     */
    private $_field_names = '';
    
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
        self::INVALID => 'Date is invalid'
    );
    
    /**
     * @param string $country_field_name
     * @return void
     */
    public function __construct($field_names) {
        $this->_field_names = $field_names;
    }
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $payment_method = (isset($context['payment_method']) ?
            $context['payment_method'] : '');
        if ($payment_method != 'credit_card') {
            return false;
        }
        $m = (isset($context[$this->_field_names['month']]) ?
            $context[$this->_field_names['month']] : '');
        $y = (isset($context[$this->_field_names['year']]) ?
            $context[$this->_field_names['year']] : '');
        if (!$m || !$y) {
            // Let "value required" validators deal with this (if any)
            return true;
        }
        if (!checkdate($m, 1, $y)) {
            $this->_error(self::INVALID);
            return false;
        }
        if (mktime(0, 0, 0, ($m + 1), 1, $y) < time()) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
