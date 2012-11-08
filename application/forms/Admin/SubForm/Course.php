<?php
/**
 * Admin course subform
 * 
 */
class Form_Admin_SubForm_Course extends Zend_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false
        ))->addElement('text', 'slug', array(
            'label'        => 'Slug',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Slug is required'
                ))
            )
        ))->addElement('checkbox', 'free', array(
            'label'        => 'Free?',
            'class'        => 'checkbox',
            'required'     => false
        ));
    }

}
