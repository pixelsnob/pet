<?php
/**
 * User profile form
 * 
 */
class Default_Form_UserProfile extends Pet_Form {
    
    /**
     * @var Model_User 
     * 
     */
    protected $_identity;
    
    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_mapper;

    /**
     * @param Model_User $identity
     * @return void
     */
    public function setIdentity(Model_User $identity) {
        $this->_identity = $identity;
    }

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('profile_form');
        // Username
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'id' => 'login-username',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Username must be %max% characters or less'
                )),

                array(new Pet_Validate_UsernameNotExists(
                    $this->_identity, $this->_mapper), true),
                array('Alnum', true, array(
                    'messages' => 'Only letters and numbers allowed'
                ))
            )
        // Email
        ))->addElement('text', 'email', array(
            'label' => 'Email',
            'id' => 'email',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your email'
                )),
                array('StringLength', true, array(
                    'max' => 75,
                    'messages' => 'Username must be %max% characters or less'
                )),
                array(new Pet_Validate_EmailNotExists(
                    $this->_identity, $this->_mapper), true),
                array(new Pet_Validate_EmailAddress)
            )
        // First name
        ))->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'id' => 'first_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your first name'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Username must be %max% characters or less'
                ))
            )
        // Last name
        ))->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'id' => 'last_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your last name'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Last name must be %max% characters or less'
                ))
            )
        // Bill address
        ))->addElement('text', 'billing_address', array(
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

        ///////////////////////////////////////////////////////////////////////
        // Shipping
        ///////////////////////////////////////////////////////////////////////
        
        // Ship first name
        ))->addElement('text', 'shipping_first_name', array(
            'label' => 'First Name',
            'id' => 'shipping_first_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your first name'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Username must be %max% characters or less'
                ))
            )
        // Ship last name
        ))->addElement('text', 'shipping_last_name', array(
            'label' => 'Last Name',
            'id' => 'shipping_last_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your last name'
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
                    'messages' => 'Please enter your address'
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
                    'messages' => 'Please enter your city'
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
        // Ship postal code
        ))->addElement('text', 'shipping_postal_code', array(
            'label' => 'Zip/Postal Code',
            'id' => 'shipping_postal_code',
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
        // Ship country
        ))->addElement('select', 'shipping_country', array(
            'label' => 'Country',
            'id' => 'shipping_country',
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
        // Ship phone
        ))->addElement('text', 'shipping_phone', array(
            'label' => 'Phone',
            'id' => 'shipping_phone',
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

        ///////////////////////////////////////////////////////////////////////
        // Profile info
        ///////////////////////////////////////////////////////////////////////
        
        // Version
        ))->addElement('select', 'version', array(
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
