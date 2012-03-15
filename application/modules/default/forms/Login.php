<?php

class Default_Form_Login extends Pet_Form {
    
    public function init() {
        $this->setMethod('post')->setName('login_form');
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
            'id' => 'login-username'
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'id' => 'login-password'
        ));
        $this->addElement('submit', 'login-submit', array(
            'label' => 'Login'
        ));
    }
}
