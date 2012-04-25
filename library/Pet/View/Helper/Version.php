<?php
/**
 * Returns a version number, to control js, css caching, etc.
 * 
 * @package Pet_View_Helper_Version
 * 
 */
class Pet_View_Helper_Version extends Zend_View_Helper_Abstract {
    
    /**
     * @return mixed
     * 
     */
    public function version() {
        $file = APPLICATION_PATH . '/../public/version';
        if (is_readable($file)) {
            $version = file_get_contents(APPLICATION_PATH . '/../public/version');
            return trim($version);
        }
    }
}
