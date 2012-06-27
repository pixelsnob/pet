<?php
/**
 * PET subform base class
 * 
 * @package Pet_Form
 * 
 */
class Pet_Form_SubForm extends Zend_Form_SubForm {
    
    /**
     * @return void
     * 
     */
    public function init() {
        $this->addElementPrefixPath('Pet_Form_Decorator', 'Pet/Form/Decorator',
            'decorator');
    }
    
}

