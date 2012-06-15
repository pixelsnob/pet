<?php
/**
 * Admin search form for admin lists
 * 
 */
class Form_Admin_Report_Sales extends Pet_Form {
    
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        
        /*$this->addElement('radio', 'output', array(
            'label' => 'Output Type',
            'id' => 'output-type',
            'class' => 'radio',
            'value' => 'file',
            'multiOptions' => array(
                'file' => 'File',
                'screen' => 'Screen'
            ),
            'required' => true,
            'separator' => '',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter a quantity'
                ))
            )
        ));*/
    }
}
