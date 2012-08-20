<?php
/**
 * @package Pet_Validate_State
 * 
 */
class Pet_Validate_State extends Zend_Validate_Abstract {
    
    /**
     * Field name of country field to look for in $context in isValid()
     * 
     * @var string
     */
    private $_country_field = '';

    /**
     * An array of states, grouped by country
     * 
     * @var array
     */
    private $_states = array();
    
    
    /**
     * Message constants
     * 
     */
    const REQUIRED       = 'required';
    const NOT_IN_COUNTRY = 'not_in_country';
    
    /**
     * @var array $_messageTemplates
     * 
     */
    protected $_messageTemplates = array(
        self::REQUIRED       => 'State is required',
        self::NOT_IN_COUNTRY => 'State is not valid'
    );

    /**
     * @param string $country_field_name
     * @return void
     */
    public function __construct($country_field, array $states) {
        $this->_country_field = $country_field;
        $this->_states = $states;
    }
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $country = (isset($context[$this->_country_field]) ?
            $context[$this->_country_field] : '');
        if ($country == 'Canada' || $country == 'USA') {
            if (!strlen(trim($value))) {
                $this->_error(self::REQUIRED);
                return false;
            }
            $states = (isset($this->_states[$country]) ?
                $this->_states[$country] : array()); 
            if (!in_array($value, array_keys($states))) {
                $this->_error(self::NOT_IN_COUNTRY);
                return false;
            }
        } elseif (strlen(trim($value)) && strlen(trim($country))) {
            $this->_error(self::NOT_IN_COUNTRY);
            return false;
        }
        return true;
    }
}
