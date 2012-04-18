<?php
/**
 * Opt-in, informational fields for user profile
 * 
 */
class Form_SubForm_UserInfo extends Zend_Form_SubForm {

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
                    'messages' => 'Phone must be %max% characters or less'
                ))
            ),
            'multiOptions' => array(
                '' => 'Please select...',
                7  => 'Version 7',
                8  => 'Version 8',
                9  => 'Version 9',
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

