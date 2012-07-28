<?php
/**
 * Forms a body class name based on controller, action, etc.
 * 
 * @package Pet_View_Helper_BodyClass
 * 
 */
class Pet_View_Helper_BodyClass extends Zend_View_Helper_Abstract {
    
    /**
     * @return string
     * 
     */
    public function bodyClass() {
        $fc = Zend_Controller_Front::getInstance();
        $module = $fc->getRequest()->getModuleName();
        $controller = $fc->getRequest()->getControllerName();
        $action = $fc->getRequest()->getActionName();
        return $this->view->escape("pet $module $controller $action");
    }
}
