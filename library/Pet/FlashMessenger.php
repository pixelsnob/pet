<?php
/**
 * A global flash messenger container
 * 
 */
class Pet_FlashMessenger {
    
    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger 
     * 
     */
    protected $_messenger;
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $fc = Zend_Controller_Front::getInstance();
        $this->_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'FlashMessenger');
    }

    public function __call($name, $args) {
        if (method_exists($this->_messenger, $name)) {
            return call_user_func_array(array($this->_messenger, $name), $args);
        }
    }

    public function addMessage($message) {
        $this->_messenger->addMessage($message);
    }

    public function __toString() {
        return $this->_messenger->__toString();
    }
}
