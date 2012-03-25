<?php
/**
 * Change password form
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
        // Username
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'id' => 'password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your password'
                )),
                array(new Pet_Validate_StoredPassword),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ))->addElement('password', 'new_password', array(
            'label' => 'New Password',
            'id' => 'new-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your new password'
                )),
                array(new Pet_Validate_NewPassword),
                array(new Pet_Validate_PasswordStrength),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
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

        // Submit
        ))->addElement('submit', 'change-pw-submit', array(
            'label' => 'Submit'
        ))->setElementFilters(array('StringTrim'));
    }
}
