<?php

class SubscribeController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_user_svc = new Service_Users;
    }

    public function indexAction() {
    }

    public function loginAction() {
        if ($this->_user_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $login_form = $this->_user_svc->getLoginForm();
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $login_form->isValid($post)) {
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

    public function profileAction() {
        if ($profile_form = $this->_user_svc->getProfileForm()) {
            $this->view->profile_form = $profile_form;
        } else {
            throw new Exception('User not found');
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $profile_form->isValid($post)) {
            $this->_user_svc->updateProfile($post);
            //exit;
        }
    }
}
