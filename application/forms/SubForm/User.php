<?php
/**
 * User form
 * 
 */
class Form_SubForm_User extends Zend_Form_SubForm {

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
        ))->setElementFilters(array('StringTrim'));

    }
    
    /**
     * @return void
     * 
     */
    public function addPasswordFields() {
        $this->addElement('password', 'password', array(
            'label' => 'New Password',
            'id' => 'password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your new password'
                )),
                array(new Pet_Validate_PasswordStrength, true),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ))->addElement('password', 'confirm_password', array(
            'label' => 'Confirm Password',
            'id' => 'confirm-password',
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
                    'messages' => 'Password and confirm password must be the same'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
        
    }
}


