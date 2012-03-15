<?php

class SubscribeController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
    }

    public function indexAction() {
        $login_form = new Default_Form_Login;
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() and $login_form->isValid($post)) {
            /*if ($this->_user_svc->authenticate($post)) {
                $this->_helper->Redirector->gotoSimple('welcome', 'login', 'university');
            } else {
                echo 'boo';
            } */  
        }
    }

    public function loginProcessAction() {
        
    }
}
