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

    protected function _initRegistryView() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->config = Zend_Registry::get('app_config');
        Zend_Registry::set('view', $view);
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

    protected function _initSession() {
        $app_config = $this->getOptions();
        $cookie_postfix = '_' . strtoupper(APPLICATION_ENV);
        Zend_Session::setOptions(array(
            'cookie_domain'   => $app_config['session_cookie_domain'],
            'cookie_lifetime' => (60 * 60 * 24 * 30),
            'name'            => 'PETSESSID' . (APPLICATION_ENV != 'production'
                                                ? $cookie_postfix : '')
        ));
        Zend_Session::start();
    }

    protected function _initForceSSL() {
        $config = $this->getOptions();
        if (!$config['use_https'] || !isset($_SERVER['HTTP_X_ORIG_PORT'])) {
            return;
        }
        $is_not_iframe = (strpos($_SERVER['QUERY_STRING'], 'iframe=') === false);
        if ($_SERVER['HTTP_X_ORIG_PORT'] != '443' && $is_not_iframe) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI']);
            exit;
        }       
    }
}

