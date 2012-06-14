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
    
    /**
     * @param string $type
     * @param string $name
     * @param array $options
     * @return Zend_Form_Element_Abstract
     * 
     */
    public function createElement($type, $name, $options = null) {
        $el = parent::createElement($type, $name, $options);
        //if ($el->getType() != 'radio') {
            $el->addDecorator('Label');
        //}
        return $el;    
    }
}
