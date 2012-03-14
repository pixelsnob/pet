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
     * File groups config
     * 
     */
    const GROUPS_CFG = '/configs/js_file_groups.php';

    /**
     * @return void
     * 
     */
    public function headScriptMin() {
        return $this;
    }
    
    /**
     * Loads a group of files from a config file
     * 
     * @param $group_name string
     * @return Pet_View_Helper_HeadScriptMin
     */
    public function loadGroup($group_name) {
        // Attempt to load config file
        $config = new Zend_Config(require APPLICATION_PATH . self::GROUPS_CFG);
        // Attempt to load group
        if (!isset($config->$group_name)) {
            $msg = "File group $group_name does not exist in " .
                self::GROUPS_CFG;
            throw new Exception($msg);
        }
        // Append files
        foreach ($config->$group_name as $file) {
            $this->appendFile($file);
        }
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
        // Replace scripts with one call to minify
        $this->appendFile(self::MIN_URL . 'f=' . implode(',', $items));
        return parent::toString();
    }
}
