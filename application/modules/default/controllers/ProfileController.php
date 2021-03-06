<?php

class ProfileController extends Zend_Controller_Action {

    public function init() {
        $this->view->getHelper('serverUrl')->setScheme('https');
        $this->_users_svc = new Service_Users;
    }

    /**
     * Profile form for logged-in users
     * 
     */
    public function indexAction() {
        if (!$this->_users_svc->isAuthenticated()) {
            $this->_helper->Redirector->gotoSimple('login');
        }
        $user = $this->_users_svc->getUser();
        $user_profile = $this->_users_svc->getProfile();
        $this->view->user = $user;
        if ($profile_form = $this->_users_svc->getProfileForm()) {
            $profile_form->billing->billing_country
                ->setValue($user_profile->billing_country)
                ->setAttrib('disabled', 'disabled');
            $profile_form->shipping->shipping_country
                ->setValue($user_profile->shipping_country)
                ->setAttrib('disabled', 'disabled');
            $this->view->profile_form = $profile_form;
        } else {
            throw new Exception('User not found');
        }
        $post = $this->_request->getPost();
        $post['billing_country'] = $user_profile->billing_country;
        $post['shipping_country'] = $user_profile->shipping_country;
        $messenger = $this->_helper->FlashMessenger;
        $messenger->setNamespace('login');
        if ($this->_request->isPost() && $profile_form->isValid($post)) {
            $this->_users_svc->updateProfile($post);
            $messenger->addMessage('Profile updated');
        } elseif ($this->_request->isPost()) {
            $messenger->addMessage('Submitted information is not valid');
        }
        $this->view->messages = $messenger->getCurrentMessages();
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
        $config = Zend_Registry::get('app_config');
        $this->_helper->FlashMessenger->setNamespace('login_form');
        $redirect_to = $this->_request->getParam('redirect_to');
        $redirect_params = (array) $this->_request->getParam('redirect_params');
        // Don't pass these in the url!
        unset($redirect_params['username']);
        unset($redirect_params['password']);
        $redirect_referer = $this->_request->getParam('redirect_referer');
        $redirect_url = $this->_request->getParam('redirect_url');
        if ($redirect_referer && !$redirect_url && isset($_SERVER['HTTP_REFERER'])) {
            $redirect_url = $_SERVER['HTTP_REFERER'];
        }
        $login_form = $this->_users_svc->getLoginForm($redirect_to,
            $redirect_params, $redirect_url);
        $login_form->setAction('/profile/login');
        // Show a different layout, configure view if this is being displayed in
        // an iframe
        if ($this->_request->getParam('iframe')) {
            $this->_helper->Layout->setLayout('iframe');
            $this->view->iframe = true;
        }
        $this->view->login_form = $login_form;
        $post = $this->_request->getParams();
        if ($this->_request->isPost() && $login_form->isValid($post) &&
        $this->_users_svc->login($post, null)) {
            $this->_users_svc->updateLastLogin();
            if ($redirect_to) {
                $this->_helper->Redirector->gotoRoute($redirect_params,
                    $redirect_to);
            } elseif ($redirect_url) {
                $this->_helper->Redirector->gotoUrl($redirect_url);
            } else {
                // Redirect to wordpress site
                $this->_helper->Redirector->gotoUrl($config['wp_url']);
            }
        } elseif ($this->_request->isPost() || $this->_request->getParam('msg')) { 
            $this->_helper->FlashMessenger->addMessage('Login failed');
            $this->view->messages = $this->_helper->FlashMessenger
                    ->getCurrentMessages();
        } else {
            $this->view->messages = $this->_helper->FlashMessenger
                ->getCurrentMessages();
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
            } else {
                $messenger = $this->_helper->FlashMessenger;
                $messenger->setNamespace('reset-pw')
                    ->addMessage('That email address was not found in our system');
                $this->view->messages = $messenger->getCurrentMessages();
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

}
