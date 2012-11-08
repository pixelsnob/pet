<?php
/**
 * Overrides Zend_Controller_Action_Helper_Redirector
 * 
 */
class Pet_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Redirector {
    
    /**
     * Override setGotoSimple for the purpose of propagating the "nolayout" param if present
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @return void
     */
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
        
        // Make sure we forward "nolayout" value in URL if it exists
        if (strpos($request->getRequestUri(), 'nolayout') !== false) {
            $params['nolayout'] = 1;
        }
        
        $router = $this->getFrontController()->getRouter();
        $url    = $router->assemble($params, 'default', true);

        $this->_redirect($url);
    }
}