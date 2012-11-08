<?php
/**
 * @package Pet_View_Helper_ServerUrl
 * 
 * 
 */
class Pet_View_Helper_ServerUrl extends Zend_View_Helper_ServerUrl {
    
    /**
     * Overrides setScheme if use_https is set to false/0 in app config
     * 
     * @return Pet_View_Helper_ServerUrl
     * 
     */
    public function setScheme($scheme) {
        $config = Zend_Registry::get('app_config');
        $use_https = (isset($config['use_https']) ? $config['use_https'] :
            true);
        $scheme = ($scheme == 'https' && !$use_https ? 'http' : $scheme);
        parent::setScheme($scheme);
        return $this;
    }
}
