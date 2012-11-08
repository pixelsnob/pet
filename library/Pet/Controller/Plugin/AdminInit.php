<?php
/**
 * Admin specific init stuff, because bootstrapping is broken in ZF
 * 
 */
class Pet_Controller_Plugin_AdminInit extends Zend_Controller_Plugin_Abstract {
    
    /**
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ($request->getModuleName() != 'admin') {
            return;
        }
        $fc = Zend_Controller_Front::getInstance();
        // Admin navigation
        $layout = $fc->getParam('bootstrap')->getResource('layout');
        $config = new Zend_Config_Xml(APPLICATION_PATH .
            '/configs/admin_nav.xml', 'nav');
        $view = $layout->getView();
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
        $view->getHelper('serverUrl')->setScheme('https');
    }

}

