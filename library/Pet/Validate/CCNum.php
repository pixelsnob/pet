<?php
/**
 * @package Pet_Validate_CCNum
 * 
 */
class Pet_Validate_CCNum extends Zend_Validate_Abstract {
    
    /**
     * Message constants
     * 
     */
    const REQUIRED = 'required';
    const INVALID = 'invalid';
    
    /**
     * @var array $_messageTemplates
     * 
     */
    protected $_messageTemplates = array(
        self::REQUIRED => 'Credit card number is required',
        self::INVALID => 'Credit card number is not valid'
    );
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $not_empty_validator = new Zend_Validate_NotEmpty;
        $payment_method = (isset($context['payment_method']) ?
            $context['payment_method'] : '');
        $is_cc = ($payment_method == 'credit_card');
        if ($is_cc && !$not_empty_validator->isValid($value)) {
            $this->_error(self::REQUIRED);
            return false;
        } elseif (!$is_cc) {
            return true;
        }
        $cc_validator = new Zend_Validate_CreditCard(array(
            Zend_Validate_CreditCard::AMERICAN_EXPRESS,
            Zend_Validate_CreditCard::VISA,
            Zend_Validate_CreditCard::MASTERCARD,
            Zend_Validate_CreditCard::DISCOVER
        ));
        if (!$cc_validator->isValid($value)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }
}
