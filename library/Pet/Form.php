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
        $this->addElementPrefixPath('Pet_Form_Decorator', 'Pet/Form/Decorator',
            'decorator');
    }
}
