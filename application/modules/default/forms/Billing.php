<?php
/**
 * Billing fields for user profile
 * 
 */
class Default_Form_Billing extends Pet_Form {

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('text', 'billing_address', array(
            'label' => 'Address',
            'id' => 'billing_address',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your address'
                )),
                array('StringLength', true, array(
                    'max' => 128,
                    'messages' => 'Address must be %max% characters or less'
                ))
            )
        // Bill address 2
        ))->addElement('text', 'billing_address_2', array(
            'label' => 'Address 2',
            'id' => 'billing_address_2',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Address 2 must be %max% characters or less'
                ))
            )
        // Bill company
        ))->addElement('text', 'billing_company', array(
            'label' => 'Company',
            'id' => 'billing_company',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => 'Company must be %max% characters or less'
                ))
            )
        // Bill city
        ))->addElement('text', 'billing_city', array(
            'label' => 'City',
            'id' => 'billing_city',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your city'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'City must be %max% characters or less'
                ))
            )
        // Bill state
        ))->addElement('select', 'billing_state', array(
            'label' => 'State',
            'id' => 'billing_state',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your state'
                )),
                array('StringLength', true, array(
                    'max' => 2,
                    'messages' => 'State must be %max% characters or less'
                ))
            )
        // Bill postal code
        ))->addElement('text', 'billing_postal_code', array(
            'label' => 'Zip/Postal Code',
            'id' => 'billing_postal_code',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your postal code'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Postal code must be %max% characters or less'
                ))
            )
        // Bill country
        ))->addElement('select', 'billing_country', array(
            'label' => 'Country',
            'id' => 'billing_country',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select your country'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Country must be %max% characters or less'
                ))
            )
        // Bill phone
        ))->addElement('text', 'billing_phone', array(
            'label' => 'Phone',
            'id' => 'billing_phone',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your phone'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Phone must be %max% characters or less'
                )),
                array(new Pet_Validate_Phone)
            )
        ))->setElementFilters(array('StringTrim'));

    }
}


