<?php
/**
 * User profile form
 * 
 */
class Default_Form_Profile extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        $this->setMethod('post')->setName('profile_form');
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
                array(new Pet_Validate_EmailNotExists)
            )
        ))->addElement('submit', 'login-submit', array(
            'label' => 'Login'
        ));
    }
}
