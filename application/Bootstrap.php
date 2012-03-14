<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    protected function _initAutoload() {
        $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
        // Override default mappers resource, which wants to use models/mappers:
        // using models/Mapper for consistency with others like models/DbTable, etc.
        $autoloader->addResourceType('mappers', 'models/Mapper', 'Model_Mapper');
        return $autoloader;
    }

    protected function _initConfig() {
        Zend_Registry::set('app_config', $this->getOptions());
    }
    
    protected function _initLogger() {
        $this->bootstrap('log');
        $log = $this->getResource('log'); 
        Zend_Registry::set('log', $log);
    }
    
    protected function _initRouter() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addConfig($config, 'routes');
    }
}

