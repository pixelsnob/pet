<?php
/**
 * Opt-in, informational fields for user profile
 * 
 */
class Form_SubForm_UserInfo extends Pet_Form_SubForm {

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        // Version
        $this->addElement('select', 'version', array(
            'label' => 'Version',
            'id' => 'version',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'This field is required. If unsure, a guess will do.'
                ))
            ),
            'multiOptions' => array(
                '' => 'Please select...',
                11  => 'Version 11',
                10  => 'Version 10',
                9  => 'Version 9',
                8  => 'Version 8',
                7  => 'Version 7',
                'other' => 'Other'
            )
        // Opt-in
        ))->addElement('checkbox', 'opt_in', array(
            'label' => 'Opt In',
            'id' => 'opt-in',
            'required' => false
        // Opt-in partner
        ))->addElement('checkbox', 'opt_in_partner', array(
            'label' => 'Opt In (Sponsors)',
            'id' => 'opt-in-partner',
            'required' => false
        ))->setElementFilters(array('StringTrim'));
    }
}


