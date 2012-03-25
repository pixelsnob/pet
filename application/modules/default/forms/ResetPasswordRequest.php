<?php
/**
 * Reset password request form
 * 
 */
class Default_Form_ResetPasswordRequest extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('reset_pw_request_form');
        $this->addElement('text', 'email', array(
            'label' => 'Email Address',
            'id' => 'email',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your email address'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                )),
                array(new Pet_Validate_EmailAddress)
            )
        // Submit
        ))->addElement('submit', 'change-pw-submit', array(
            'label' => 'Submit'
        ))->setElementFilters(array('StringTrim'));
    }
}
