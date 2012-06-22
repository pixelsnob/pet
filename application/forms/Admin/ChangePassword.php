<?php
/**
 * Change password form for logged in users
 * 
 */
class Form_Admin_ChangePassword extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        // New password
        $this->addElement('password', 'new_password', array(
            'label' => 'New Password',
            'id' => 'new-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter a password'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                )),
                array(new Pet_Validate_PasswordStrength, true),
            )
        // Confirm new password
        ))->addElement('password', 'confirm_new_password', array(
            'label' => 'Confirm Password',
            'id' => 'confirm-new-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please confirm password'
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
    
}
