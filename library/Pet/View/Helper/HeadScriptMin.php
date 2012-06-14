<?php
/**
 * @package Pet_View_Helper_HeadScriptMin
 * 
 * Takes files added via appendFile(), converts them to a list, and calls the minify url
 * Any code added via appendScript() will be ignored.
 * 
 */
require_once 'Pet/View/Helper/HeadScript.php';

class Pet_View_Helper_HeadScriptMin extends Pet_View_Helper_HeadScript {
    
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
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Pet_View_Helper_HeadScriptMin';

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
        $files = array();
        $scripts = '';
        foreach ($this as $item) {
            if (!$this->_isValid($item)) {
                continue;
            }
            // Determine whether this is a file or a script. Store data
            // accordingly
            if (isset($item->attributes['src'])) {
                $files[] = $item->attributes['src'];
            } elseif (isset($item->source)) {
                $scripts .= $item->source . "\n";
            }
        }
        // Clear stack of scripts
        $this->getContainer()->exchangeArray(array());
        // Append version, to control caching
        $version = $this->view->version();
        $version = ($version ? '?version=' . $version : '');
        if (count($files)) {
            $url = self::MIN_URL . 'f=' . implode(',', $files) . $version;
            // Replace scripts with one call to minify
            $this->appendFile($url);
        }
        $this->appendScript($scripts);
        return parent::toString();
    }
}
