<?php
/**
 * Returns a version number, to control js, css caching, etc.
 * 
 * @package Pet_View_Helper_Version
 * 
 */
class Pet_View_Helper_Version extends Pet_View_Helper_HeadScript {
    
    /**
     * @return string
     * 
     */
    public function version() {
        $version = file_get_contents(APPLICATION_PATH . '/../public/version');
        return trim($version);
    }
}
