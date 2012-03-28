<?php
/**
 * Adds a param to disable the layout if front controller param is set
 * 
 * 
 */
class Pet_View_Helper_Url extends Zend_View_Helper_Url {
    
    /**
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @param  bool $encode
     * @return string Url for the link href attribute.
     * 
     */
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true,
                        $https = false) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $layout = parent::url($urlOptions, $name, $reset, $encode);
        if (strpos($request->getRequestUri(), 'nolayout') !== false) {
            $layout .= '?nolayout';
        }
        return $layout;
    }
}
