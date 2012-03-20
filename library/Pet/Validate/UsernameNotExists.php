<?php
/**
 * @package Pet_Validate_UsernameNotExists
 * 
 */
class Pet_Validate_UsernameNotExists extends Zend_Validate_Abstract {
    
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
        self::INVALID => 'That username is not available'
    );
    
    /**
     * @param string $country_field_name
     * @return void
     */
    /*public function __construct($field_names) {
        $this->_field_names = $field_names;
    }*/
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $users = new Model_Mapper_Users;
        $user = $users->getByUsername($value);
        if ($user && $user->id && $user->id != $identity->id) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
