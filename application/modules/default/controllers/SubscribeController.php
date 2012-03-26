<?php

class SubscribeController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_users_svc = new Service_Users;
    }

    public function indexAction() {
    }

    public function loginAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $login_form = $this->_users_svc->getLoginForm();
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $login_form->isValid($post)) {
            if ($this->_users_svc->authenticate($post)) {
                $this->_users_svc->updateLastLogin();
                $this->_helper->Redirector->gotoSimple('index');
            } else {
                $this->view->login_failed = true;
            } 
        }
    }
    
    public function logoutAction() {
        $this->_users_svc->logout(); 
        $this->_helper->Redirector->gotoSimple('login');
    }

    public function profileAction() {
        $this->view->headLink()->appendStylesheet('/css/profile.css');
        if (!$this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        if ($profile_form = $this->_users_svc->getProfileForm()) {
            $this->view->profile_form = $profile_form;
        } else {
            throw new Exception('User not found');
        }
        if ($subscription = $this->_users_svc->getSubscription()) {
            $this->view->subscription = $subscription;
        } else {
            throw new Exception('User subscription not found');
        }
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $profile_form->isValid($post)) {
            $this->_users_svc->updateProfile($post);
            $this->view->profile_updated = true;
        }
    }

    public function changePasswordAction() {
        if (!$this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        if ($pw_form = $this->_users_svc->getChangePasswordForm()) {
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
            $this->_users_svc->updatePassword($new_pw);
            $this->view->password_updated = true;
            $pw_form->reset();
        }
    }

    public function resetPasswordRequestAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('change-password');
        }
        $pw_form = $this->_users_svc->getResetPasswordRequestForm();
        $this->view->pw_form = $pw_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $pw_form->isValid($post)) {
            if ($user = $this->_users_svc->getActiveUserByEmail($post['email'])) {
                $this->_users_svc->resetPasswordRequest($user);
                $this->_helper->Redirector->gotoSimple(
                    'reset-password-request-success');
            } else {
                $this->view->email_invalid = true;
            }
        }
    }

    public function resetPasswordRequestSuccessAction() {
        
    }

    /**
     * For non-logged-in users
     * 
     */
    public function resetPasswordAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('change-password');
        }
        $token = $this->_request->getParam('token');
        $db_token = $this->_users_svc->getValidPasswordResetToken($token);
        if (!$db_token) {
            $this->view->invalid = true;
            return;
        }
        $pw_form = $this->_users_svc->getResetPasswordForm($db_token->user_id);
        $this->view->pw_form = $pw_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $pw_form->isValid($post)) {
            $new_pw = $this->_request->getPost('password');
            $this->_users_svc->resetPassword($new_pw, $token);    
            exit('success');
        }
    }
}
