<?php
/**
 * @package Pet_View_Helper_HeadScriptMin
 * 
 * Takes files added view appendFile(), converts them to a list, and calls the minify url
 * 
 * 
 */
class Pet_View_Helper_HeadScriptMin extends Zend_View_Helper_HeadScript {
    
    /**
     * Minify url
     * 
     */
    const MIN_URL = '/min/';
    
    /**
     * @return void
     * 
     */
    public function headScriptMin() {
        return $this;
    }
    
    /**
     * @return string
     * 
     */
    public function toString() {
        if (!$this->getContainer()->count()) {
            return '';
        }
        $this->getContainer()->ksort();
        $items = array();
        foreach ($this as $item) {
            // Ignore scripts. We're only interested in files.
            if (!$this->_isValid($item) || !isset($item->attributes['src'])) {
                continue;
            }
            $items[] = $item->attributes['src'];
        }
        // Clear stack of scripts
        $this->getContainer()->exchangeArray(array());
        $this->appendFile(self::MIN_URL . 'f=' . implode(',', $items));
        return parent::toString();
    }
}
