<?php

class Pet_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Redirector {
    
    public function setGotoSimple($action, $controller = null, $module = null,
                                  array $params = array()) {
        
        $dispatcher = $this->getFrontController()->getDispatcher();
        $request    = $this->getRequest();
        $curModule  = $request->getModuleName();
        $useDefaultController = false;

        if (null === $controller && null !== $module) {
            $useDefaultController = true;
        }

        if (null === $module) {
            $module = $curModule;
        }

        if ($module == $dispatcher->getDefaultModule()) {
            $module = '';
        }

        if (null === $controller && !$useDefaultController) {
            $controller = $request->getControllerName();
            if (empty($controller)) {
                $controller = $dispatcher->getDefaultControllerName();
            }
        }

        $params[$request->getModuleKey()]     = $module;
        $params[$request->getControllerKey()] = $controller;
        $params[$request->getActionKey()]     = $action;
        
        //$params['nolayout'] = 1;
        
        $router = $this->getFrontController()->getRouter();
        $url    = $router->assemble($params, 'default', true);

        $this->_redirect($url);
    }
}