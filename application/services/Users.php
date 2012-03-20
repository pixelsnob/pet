<?php
/**
 * Users service layer
 *
 * @package Service_Users
 * 
 */
class Service_Users {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_user_profiles = new Model_Mapper_UserProfiles;
        $this->_users = new Model_Mapper_Users;
    }

    /**
     * @param $data Username and password, etc.
     * @return bool Auth status
     * 
     * 
     */
    public function authenticate($data) {
        $username = (isset($data['username']) ? $data['username'] : '');
        $password = (isset($data['password']) ? $data['password'] : '');
        $auth_adapter = new Pet_Auth_Adapter($username, $password);
        $auth = Zend_Auth::getInstance();
        return $auth->authenticate($auth_adapter)->isValid();
    }
    
    /**
     * @return void
     * 
     */
    public function logout() {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * @return bool Auth status
     * 
     */
    public function isAuthenticated() {
        return Zend_Auth::getInstance()->hasIdentity();
    }
    
    public function getUser() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_users->getById($identity->id);
    }

    public function getProfile() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_user_profiles->getByUserId($identity->id);
    }
    
    public function getProfileForm() {
        $profile_form = new Default_Form_Profile;
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$identity) {
            return false;
        }
        $user = $this->getUser();
        $profile = $this->getProfile();
        if ($user && $profile) {
            $form_data = array_merge($user->toArray(), $profile->toArray());
            $profile_form->populate($form_data);
            return $profile_form;
        }
        return false;
    }
    
    public function updateProfile($data) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $profile = $this->getProfile();
        if (!$profile) {
            throw new Exception('Profile by ' . $identity->id . ' not found');
        }
        $this->_users->save($data, $identity->id);
    }

    
    public function getLoginForm() {
        $login_form = new Default_Form_Login; 
        return $login_form;
    }
}
