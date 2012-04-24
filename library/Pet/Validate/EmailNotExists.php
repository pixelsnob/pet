<?php
/**
 * @package Pet_Validate_EmailNotExists
 * 
 */
class Pet_Validate_EmailNotExists extends Zend_Validate_Abstract {
    
    /**
     * @var Model_User
     * 
     */
    protected $_identity;
    
    /**
     * @var Pet_Model_Mapper_Abstract
     * 
     */
    protected $_mapper;

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
        self::INVALID => 'That email is already in use'
    );

    /**
     * @param Model_User $identity
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     * 
     */
    public function __construct($identity, $mapper) {
        $this->_identity = $identity;
        $this->_mapper = $mapper;
    }
    
    /**
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $user = $this->_mapper->getByEmail($value);
        
        if ($user && $user->id && ($this->_identity && $user->id != $this->_identity->id || !$this->_identity)) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
