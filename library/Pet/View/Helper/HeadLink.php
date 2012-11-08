<?php
/**
 * Appends version param to urls
 * 
 * @package Pet_View_Helper_HeadLink
 * 
 */
class Pet_View_Helper_HeadLink extends Zend_View_Helper_HeadLink {
    
    /**
     * @return string
     * 
     */
    public function itemToString(stdClass $item) { 
        if (isset($item->href)) {
            $version = $this->view->version();
            $version = ($version ? '?version=' . $version : '');
            $item->href .= $version;
        }
        return parent::itemToString($item);
    }
}
