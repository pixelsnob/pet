<?php
/**
 * User profile form
 * 
 */
class Default_Form_UserProfile extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        $this->setMethod('post')->setName('profile_form');
        $this->setElementFilters(array('StringTrim'));
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'id' => 'login-username',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username'
                )),
                array(new Pet_Validate_UsernameNotExists)
            )
        ))->addElement('text', 'email', array(
            'label' => 'Email',
            'id' => 'email',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your email'
                )),
                array(new Pet_Validate_EmailNotExists),
                array(new Pet_Validate_EmailAddress)
            )
        ))->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'id' => 'first_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your first name'
                ))
            )
        ))->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'id' => 'last_name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your last name'
                ))
            )
        ))->addElement('text', 'billing_address', array(
            'label' => 'Address',
            'id' => 'billing_address',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your address'
                ))
            )
        ))->addElement('text', 'billing_address_2', array(
            'label' => 'Address 2',
            'id' => 'billing_address_2',
            'required' => false
        ))->addElement('text', 'billing_company', array(
            'label' => 'Company',
            'id' => 'billing_company',
            'required' => false
        ))->addElement('text', 'billing_city', array(
            'label' => 'City',
            'id' => 'billing_city',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your city'
                ))
            )
        ))->addElement('select', 'billing_state', array(
            'label' => 'State',
            'id' => 'billing_state',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your state'
                ))
            )
        ))->addElement('text', 'billing_postal_code', array(
            'label' => 'Zip/Postal Code',
            'id' => 'billing_postal_code',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your postal code'
                ))
            )
        ))->addElement('text', 'billing_country', array(
            'label' => 'Country',
            'id' => 'billing_country',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your country'
                ))
            )
        ))->addElement('text', 'billing_phone', array(
            'label' => 'Phone',
            'id' => 'billing_phone',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your phone'
                ))
            )
        ))->addElement('submit', 'login-submit', array(
            'label' => 'Login'
        ));
    }
}
