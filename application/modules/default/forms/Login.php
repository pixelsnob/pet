<?php
/**
 * Login form
 * 
 */
class Default_Form_Login extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        $this->setMethod('post')->setName('login_form');
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'id' => 'login-username',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username'
                ))
            )
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'id' => 'login-password',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your password'
                ))
            )
        ));
        $this->addElement('submit', 'login-submit', array(
            'label' => 'Login'
        ));
    }
}
