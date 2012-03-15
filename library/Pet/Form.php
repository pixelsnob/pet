<?php
/**
 * PET form base class
 * 
 * @package Pet_Form
 * 
 */
class Pet_Form extends Zend_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        // Add custom plugin paths
        $this->addPrefixPath('Pet_Form_Element', 'Pet/Form/Element/', 'element');
        parent::init();
    }
}
