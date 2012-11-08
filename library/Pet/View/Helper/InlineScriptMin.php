<?php
/**
 * @package Pet_View_Helper_InlineScriptMin
 * 
 * See Pet_View_Helper_HeadScriptMin
 * 
 */

class Pet_View_Helper_InlineScriptMin extends Pet_View_Helper_HeadScriptMin {
    
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Pet_View_Helper_InlineScriptMin';

    /**
     * @return void
     * 
     */
    public function inlineScriptMin() {
        return $this;
    }

}
