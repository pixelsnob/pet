<?php
/**
 * Admin search form for admin lists
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
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => 'Search term must be %max% characters or less'
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
                    'messages' => 'Start date must be %max% characters or less'
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
                    'messages' => 'End date must be %max% characters or less'
                ))
            )
        ))->addElement('hidden', 'sort')
          ->addElement('hidden', 'sort_dir')
          ->setElementFilters(array('StringTrim'));
    }
}
