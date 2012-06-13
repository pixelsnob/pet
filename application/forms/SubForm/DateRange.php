<?php
/**
 * Date range subform
 * 
 */
class Form_SubForm_DateRange extends Zend_Form_SubForm {

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('text', 'start_date', array(
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
        ))->setElementFilters(array('StringTrim'));

    }
}


