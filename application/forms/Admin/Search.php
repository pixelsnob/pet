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
        ))->addElement('hidden', 'sort')
          ->addElement('hidden', 'sort_dir')
          ->setElementFilters(array('StringTrim'));
        
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
    }
}
