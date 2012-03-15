<?php

class Default_Form_Login extends Pet_Form {
    
    public function init() {
        $this->setMethod('post')->setName('login_form');
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true
        ));
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true
        ));
        $this->addElement('submit', 'login', array(
            'label' => 'Login'
        ));
    }
}
