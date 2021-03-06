<?php
/**
 * Shipping fields for user profile
 * 
 */
class Form_SubForm_Shipping extends Pet_Form_SubForm {

    /**
     * @var array
     * 
     */
    protected $_countries;

    /**
     * @var array
     * 
     */
    protected $_states;

    /**
     * @param array $countries
     * @return void
     */
    public function setCountries(array $countries) {
        $this->_countries = $countries;
    }

    /**
     * @param array $countries
     * @return void
     */
    public function setStates(array $states) {
        $this->_states = $states;
    }


    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('text', 'shipping_first_name', array(
            'label' => 'First Name',
            'id' => 'shipping_first_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'First name is required'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'First name must be %max% characters or less'
                ))
            )
        // Ship last name
        ))->addElement('text', 'shipping_last_name', array(
            'label' => 'Last Name',
            'id' => 'shipping_last_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Last name is required'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Last name must be %max% characters or less'
                ))
            )
        // Ship address
        ))->addElement('text', 'shipping_address', array(
            'label' => 'Address',
            'id' => 'shipping_address',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Address is required'
                )),
                array('StringLength', true, array(
                    'max' => 128,
                    'messages' => 'Address must be %max% characters or less'
                ))
            )
        // Ship address 2
        ))->addElement('text', 'shipping_address_2', array(
            'label' => 'Address 2',
            'id' => 'shipping_address_2',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Address 2 must be %max% characters or less'
                ))
            )
        // Ship company
        ))->addElement('text', 'shipping_company', array(
            'label' => 'Company',
            'id' => 'shipping_company',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => 'Company must be %max% characters or less'
                ))
            )
        // Ship city
        ))->addElement('text', 'shipping_city', array(
            'label' => 'City',
            'id' => 'shipping_city',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'City is required'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'City must be %max% characters or less'
                ))
            )
        // Ship state
        ))->addElement('select', 'shipping_state', array(
            'label' => 'State',
            'id' => 'shipping_state',
            'required' => false,
            'allowEmpty' => false,
            'validators'   => array(
                array(new Pet_Validate_State('shipping_country', $this->_states))
            )
        // Ship postal code
        ))->addElement('text', 'shipping_postal_code', array(
            'label' => 'Zip/Postal Code',
            'id' => 'shipping_postal_code',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Postal code is required'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Postal code must be %max% characters or less'
                ))
            )
        // Ship country
        ))->addElement('select', 'shipping_country', array(
            'label' => 'Country',
            'id' => 'shipping_country',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Country is required'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Country must be %max% characters or less'
                ))
            )
        // Ship phone
        ))->addElement('text', 'shipping_phone', array(
            'label' => 'Phone',
            'id' => 'shipping_phone',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Phone must be %max% characters or less'
                )),
                array(new Pet_Validate_Phone)
            )

        ))->setElementFilters(array('StringTrim'));
        if (!empty($this->_countries)) {
           $this->shipping_country->setMultiOptions($this->_countries); 
        }
        if (!empty($this->_states)) {
           $this->shipping_state->setMultiOptions($this->_states); 
        }

    }

    public function isValid($data) {
        return parent::isValid($data);
    }
}


