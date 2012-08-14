<?php
/**
 * User form
 * 
 */
class Form_SubForm_User extends Pet_Form_SubForm {

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
     * @param mixed $identity
     * @return void
     */
    public function setIdentity($identity) {
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
        // Username
        $this->addElement('text', 'username', array(
            'label' => 'User Name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please create a user name for the website'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Username must be %max% characters or less'
                )),

                array(new Pet_Validate_UsernameNotExists(
                    $this->_identity, $this->_mapper), true),
                array('Alnum', true, array(
                    'messages' => 'Please only use letters and numbers here'
                ))
            )
        // Email
        ))->addElement('text', 'email', array(
            'label' => 'Email Address',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Email address is required'
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
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'First name is required'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Username must be %max% characters or less'
                ))
            )
        // Last name
        ))->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Last name is required'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Last name must be %max% characters or less'
                ))
            )
        ))->addElement('password', 'password', array(
            'label' => 'Create a Password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please create a password for website access'
                )),
                array(new Pet_Validate_PasswordStrength, true),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ))->addElement('password', 'confirm_password', array(
            'label' => 'Confirm Password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please confirm your password'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                )),
                array('Identical', true, array(
                    'token' => 'password',
                    'messages' => 'Whoops! Those don\'t match'
                ))
            )
        ))->setElementFilters(array('StringTrim'));

    }
    
}


