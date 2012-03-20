<?php
/**
 * @package Pet_Validate_EmailNotExists
 * 
 */
class Pet_Validate_EmailNotExists extends Zend_Validate_Abstract {
    
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
     * isValid() implementation
     * 
     * @param mixed $value
     * @param mixed $context
     * @return bool
     */
    public function isValid($value, $context = null) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $users = new Model_Mapper_Users;
        $user = $users->getByEmail($value);
        if ($user && $user->id && $user->id != $identity->id) {
            $this->_error(self::INVALID);
            return false;    
        }
        return true;
    }
}
