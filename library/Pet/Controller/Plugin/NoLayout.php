<?php
/**
 * Sets layout to blank.phtml if ?nolayout is present in the url
 * 
 */
class Pet_Controller_Plugin_NoLayout extends Zend_Controller_Plugin_Abstract {
    
    /**
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $fc = Zend_Controller_Front::getInstance();
        if (strpos($request->getRequestUri(), 'nolayout') !== false) {
            $layout = $fc->getParam('bootstrap')->getResource('layout');
            $layout->setLayout('nolayout');
            $request->setParam('nolayout', true);
        }
    }

}
