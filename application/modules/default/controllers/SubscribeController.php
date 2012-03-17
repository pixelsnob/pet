<?php

class SubscribeController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_user_svc = new Service_User;
    }

    public function indexAction() {
    }

    public function loginAction() {
        if ($this->_user_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('welcome');
        }
        $login_form = new Default_Form_Login;
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() and $login_form->isValid($post)) {
            if ($this->_user_svc->authenticate($post)) {
                $this->_helper->Redirector->gotoSimple('index');
            } else {
                $this->view->login_failed = true;
            } 
        }
    }
    
    public function logoutAction() {
        $this->_user_svc->logout(); 
        $this->_helper->Redirector->gotoSimple('login');
    }

    public function welcomeAction() {
        if (!$this->_user_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        exit('hi');
    }

}
