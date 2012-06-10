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
        ))->addElement('text', 'start_date', array(
            'label' => 'Start date',
            'id' => 'start_date',
            'class' => 'datepicker',
            'required' => false,
            'validators'   => array(
                array('Date', true, array(
                    'messages' => 'Invalid date'
                )),
                array('StringLength', true, array(
                    'max' => 10,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ))->addElement('text', 'end_date', array(
            'label' => 'End date',
            'id' => 'end_date',
            'class' => 'datepicker',
            'required' => false,
            'validators'   => array(
                array('Date', true, array(
                    'messages' => 'Invalid date'
                )),
                array('StringLength', true, array(
                    'max' => 10,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ));
    }
}
