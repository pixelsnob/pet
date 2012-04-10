<?php

class ProfileController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_users_svc = new Service_Users;
        $this->_messenger = Zend_Registry::get('messenger');
        $this->_messenger->setNamespace('profile');
        $this->view->headLink()->appendStylesheet('/css/profile.css');
    }

    /**
     * Profile form for logged-in users
     * 
     */
    public function indexAction() {
        if (!$this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        if ($profile_form = $this->_users_svc->getProfileForm()) {
            $this->view->profile_form = $profile_form;
        } else {
            throw new Exception('User not found');
        }
        /*if ($subscription = $this->_users_svc->getSubscription()) {
            $this->view->subscription = $subscription;
        } else {
            throw new Exception('User subscription not found');
        }*/
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $profile_form->isValid($post)) {
            $this->_users_svc->updateProfile($post);
            $this->_messenger->addMessage('Profile updated');
        } elseif ($this->_request->isPost()) {
            $this->_messenger->addMessage('Submitted information is not valid');
        }
        $this->view->inlineScriptMin()->loadGroup('profile')
            ->appendScript('new Pet.ProfileFormView;');
    }

    /**
     * Shows the login form
     * 
     */
    public function loginAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $redirect = $this->_request->getParam('redirect_to');
        $redirect_params = (array) $this->_request->getParam('redirect_params');
        $login_form = $this->_users_svc->getLoginForm();
        $this->view->login_form = $login_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost() && $login_form->isValid($post)) {
            if ($this->_users_svc->login($post)) {
                $this->_users_svc->updateLastLogin();
                $this->_users_svc->logUserAction('User logged in');
                if ($redirect) {
                    $this->_helper->Redirector->gotoRoute($redirect_params,
                        $redirect);
                } else {
                    $this->_helper->Redirector->gotoSimple('index');
                }
            } else {
                $messenger = Zend_Registry::get('messenger');
                $messenger->setNamespace('login');
                $messenger->addMessage('Login failed');
            } 
        }
    }
    
    /**
     * Logs out a user
     * 
     */
    public function logoutAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_users_svc->logout(); 
        }
        $this->_helper->Redirector->gotoSimple('login');
    }

    /**
     * Change password for logged-in user
     * 
     */
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
            $this->_helper->Redirector->gotoSimple('change-password-success');

        }
    }
    
    public function changePasswordSuccessAction() {}

    /**
     * Simple form that accepts a user's email address
     * 
     */
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
            }
        }
    }

    public function resetPasswordRequestSuccessAction() {}

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
            $this->_users_svc->resetPasswordByToken($new_pw, $token);    
            $this->_helper->Redirector->gotoSimple(
                'reset-password-success');
        }
    }

    public function resetPasswordSuccessAction() {}

    public function isAuthenticatedAction() {
        $this->_helper->json(array('is_authenticated' =>
            $this->_users_svc->isAuthenticated()));
    }

    public function timeoutAction() {

    }
}
