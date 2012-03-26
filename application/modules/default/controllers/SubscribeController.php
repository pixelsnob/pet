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
                $this->_user_svc->updateLastLogin();
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
        $this->view->headLink()->appendStylesheet('/css/profile.css');
        if (!$this->_user_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        if ($profile_form = $this->_user_svc->getProfileForm()) {
            $this->view->profile_form = $profile_form;
        } else {
            throw new Exception('User not found');
        }
        if ($subscription = $this->_user_svc->getSubscription()) {
            $this->view->subscription = $subscription;
        } else {
            throw new Exception('User subscription not found');
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $profile_form->isValid($post)) {
            $this->_user_svc->updateProfile($post);
            $this->view->profile_updated = true;
        }
    }

    public function changePasswordAction() {
        if (!$this->_user_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        if ($pw_form = $this->_user_svc->getChangePasswordForm()) {
            $this->view->pw_form = $pw_form;
        } else {
            throw new Exception('User not found');
        }
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $pw_form->populate($post);
        }
        if ($this->_request->isPost() && $pw_form->isValid($post)) {
            $new_pw = $this->_request->getPost('new_password');
            $this->_user_svc->updatePassword($new_pw);
            $this->view->password_updated = true;
            $pw_form->reset();
        }
    }

    public function resetPasswordRequestAction() {
        $pw_form = $this->_user_svc->getResetPasswordRequestForm();
        $this->view->pw_form = $pw_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $pw_form->isValid($post)) {
            if ($user = $this->_user_svc->getActiveUserByEmail($post['email'])) {
                $this->_user_svc->resetPasswordRequest($user);
                $this->_helper->Redirector->gotoSimple(
                    'reset-password-request-success');
            } else {
                $this->view->email_invalid = true;
            }
        }
    }

    public function resetPasswordRequestSuccessAction() {
        
    }

    public function resetPasswordAction() {
        $token = $this->_request->getParam('token');
        if (!$this->_user_svc->getValidPasswordResetToken($token)) {
            $this->view->invalid = true;
            return;
        }
        $pw_form = $this->_user_svc->getResetPasswordForm();
        $this->view->pw_form = $pw_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $pw_form->isValid($post)) {
            $new_pw = $this->_request->getPost('password');
            $this->_user_svc->resetPassword($new_pw, $token);    
            exit('success');
        }
    }
}
