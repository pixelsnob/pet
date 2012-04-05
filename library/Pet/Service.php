<?php
/**
 * Service base class
 *
 * @package Pet_Service
 * 
 */
class Pet_Service {
    
    /**
     * @param string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        
    }
    
    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
    
}
