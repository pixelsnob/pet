<?php
/**
 * Admin digital subscription subform
 * 
 */
class Form_Admin_SubForm_DigitalSubscription extends Zend_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false
        ))->addElement('checkbox', 'is_renewal', array(
            'label'        => 'Renewal?',
            'class'        => 'checkbox',
            'required'     => false
        ))->addElement('checkbox', 'is_recurring', array(
            'label'        => 'Recurring?',
            'class'        => 'checkbox',
            'required'     => false
        ));

    }

}
