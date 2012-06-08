<?php

class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->Layout->setLayout('admin');
        $this->_users_svc = new Service_Users;
    }
    
    public function indexAction() {
        $login_form = $this->_users_svc->getLoginForm();
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $login_form->isValid($post)) {
            if ($this->_users_svc->login($post, true)) {
                $this->_users_svc->updateLastLogin();
                $this->_helper->Redirector->gotoSimple('home');
            } else {
                $this->_helper->FlashMessenger->addMessage('Login failed');
                $this->view->messages = $this->_helper->FlashMessenger
                    ->getCurrentMessages();
            } 
        }
    }

    public function homeAction() {
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $this->_helper->ViewRenderer->setNoRender(true);
    }

    public function logoutAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_users_svc->logout(); 
        }
        $this->_helper->Redirector->gotoSimple('index');
    }

}
