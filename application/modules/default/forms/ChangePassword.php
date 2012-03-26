<?php
/**
 * Change password form for logged in users
 * 
 */
class Default_Form_ChangePassword extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('change_pw_form');
        // Password
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'id' => 'password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your password'
                )),
                array('Callback', true, array(
                    'callback' => array($this, 'isExistingPasswordValid'),
                    'messages' => 'Password is not correct'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        // New password
        ))->addElement('password', 'new_password', array(
            'label' => 'New Password',
            'id' => 'new-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your new password'
                )),
                array('Callback', true, array(
                    'callback' => array($this, 'isNewPasswordValid'),
                    'messages' => 'New password must be different than old password'
                )),
                array(new Pet_Validate_PasswordStrength, true),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        // Confirm new password
        ))->addElement('password', 'confirm_new_password', array(
            'label' => 'Confirm New Password',
            'id' => 'confirm-new-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please confirm your new password'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                )),
                array('Identical', true, array(
                    'token' => 'new_password',
                    'messages' => 'Password and confirm password must be the same'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }
    
    /**
     * @param string $value
     * @param array $context
     * @return bool 
     * 
     */
    public function isExistingPasswordValid($value, $context) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $users_svc = new Service_Users;
        return $users_svc->validatePassword($identity->password, $value);
    }

    /**
     * @param string $value
     * @param array $context
     * @return bool 
     * 
     */
    public function isNewPasswordValid($value, $context) {
        $existing_pw = (isset($context['password']) ? $context['password'] : '');
        return !(trim($existing_pw) == trim($value));
    }
}
