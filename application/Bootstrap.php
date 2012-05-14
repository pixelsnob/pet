<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    protected function _initAutoload() {
        $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
        $autoloader->addResourceType('mappers', 'models/Mapper', 'Model_Mapper')
            ->addResourceType('service', 'services', 'Service')
            ->addResourceType('form', 'forms', 'Form')
            ->addResourceType('model', 'models', 'Model');
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
    
    protected function _initActionHelpers() {
        Zend_Controller_Action_HelperBroker::addPrefix(
            'Pet_Controller_Action_Helper');
    }

    protected function _initMongo() {
        $config = $this->getOptions();
        Pet_Mongo::setConnectionUri($config['mongo']['connection_uri']);
        Pet_Mongo::setDb($config['mongo']['db']);
    }
}

