<?php
/**
 * Loads the admin nav object if in the admin module
 * 
 */
class Pet_Controller_Plugin_AdminNav extends Zend_Controller_Plugin_Abstract {
    
    /**
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $fc = Zend_Controller_Front::getInstance();
        $layout = $fc->getParam('bootstrap')->getResource('layout');
        $config = new Zend_Config_Xml(APPLICATION_PATH .
            '/configs/admin_nav.xml', 'nav');
        $view = $layout->getView();
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }

}

