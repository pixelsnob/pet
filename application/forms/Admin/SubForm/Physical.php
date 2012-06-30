<?php
/**
 * Admin physical product subform
 * 
 */
class Form_Admin_SubForm_Physical extends Zend_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false
        ));
    }

}
