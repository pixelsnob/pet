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
        $this->addElement('checkbox', 'is_renewal', array(
            'label'        => 'Renewal?',
            'class'        => 'checkbox',
            'required'     => false
        ))->addElement('checkbox', 'is_recurring', array(
            'label'        => 'Recurring?',
            'class'        => 'checkbox',
            'required'     => false
        ))->addElement('text', 'term_months', array(
            'label'        => 'Term (months)',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Term is required'
                )),
                array('Digits', true, array(
                    'messages' => 'Please enter a positive number'
                )),
                array('LessThan', true, array(
                    'max'      => 360,
                    'messages' => 'Term must be less than %max%'
                ))
            )
        ));
    }

}
