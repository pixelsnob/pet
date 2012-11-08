<?php
/**
 * @package Pet_View_Helper_HeadScript
 * 
 * 
 */
class Pet_View_Helper_HeadScript extends Zend_View_Helper_HeadScript {
    
    /**
     * @return string
     * 
     */
    public function toString() {
        // Remove dumb unnecessary html comments that headScript()
        // insists on using
        $out = parent::toString();
        $out = str_replace(array('//<!--', '//-->'), '', $out);
        return $out;
    }
}
