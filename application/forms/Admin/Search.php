<?php
/**
 * Admin search form for lists
 * 
 */
class Form_Admin_Search extends Pet_Form {
    
    public function init() {
        parent::init();
        $this->addElement('text', 'search', array(
            'label' => 'Search term',
            'id' => 'search',
            'required' => false,
            'validators'   => array(
                /*array('NotEmpty', true, array(
                    'messages' => 'Please enter your password'
                )),*/
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        )); 
    }
}
