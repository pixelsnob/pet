<?php
/**
 * @package Pet_Validate_EmailAddress
 * 
 */
class Pet_Validate_EmailAddress extends Zend_Validate_EmailAddress {
    
    /**
     * Overrides getMessages so that only one message is returned
     * 
     */
    public function getMessages() {
        return array(0 => 'Email address is not valid');
    }
}
