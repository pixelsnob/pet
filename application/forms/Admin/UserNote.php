<?php
/**
 * User note form
 * 
 */
class Form_Admin_UserNote extends Pet_Form {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setName('user_note_edit');
        $this->addElement('textarea', 'note', array(
            'label'        => 'Note',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Note text is required'
                )),
                array('StringLength', true, array(
                    'max' => 255,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->addElement('hidden', 'user_id')
          ->setElementFilters(array('StringTrim'));
    }
    

}
