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
        $this->_user_subs = new Model_Mapper_UserSubscriptions;
        $this->_user_actions = new Model_Mapper_UserActions;
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
    
    /**
     * @return Model_User 
     * 
     */
    public function getUser() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_users->getById($identity->id);
    }

    /**
     * @return Model_UserProfile 
     * 
     */
    public function getProfile() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_user_profiles->getByUserId($identity->id);
    }
    
    /**
     * @return bool|Default_Form_UserProfile
     * 
     */
    public function getProfileForm() {
        $profile_form = new Default_Form_UserProfile;
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$identity) {
            return false;
        }
        $user = $this->getUser();
        $profile = $this->getProfile();
        if ($user && $profile) {
            $states = new Zend_Config(require APPLICATION_PATH .
                '/configs/states.php');
            $profile_form->billing_state->setMultiOptions($states->toArray());
            $profile_form->shipping_state->setMultiOptions($states->toArray());
            $form_data = array_merge($user->toArray(), $profile->toArray());
            $profile_form->populate($form_data);
            return $profile_form;
        }
        return false;
    }
    
    /**
     * @return Model_UserSubscription 
     * 
     */
    public function getSubscription() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_user_subs->getByUserId($identity->id);
    }

    /**
     * @param array $data
     * @return bool Update status
     * 
     */
    public function updateProfile(array $data) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $this->_users->updatePersonal($data, $identity->id);
            $this->_user_profiles->updateByUserId($data, $identity->id);
            $auth_storage = Zend_Auth::getInstance()->getStorage();
            $auth_storage->write($this->getUser());
            $db->commit();
            $this->logUserAction('Profile updated');
            return true;
        } catch (Exception $e) {
            try {
                $db->rollBack();
            } catch (Exception $e2) {}
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @return void
     * 
     */
    public function updateLastLogin() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->_users->updateLastLogin($identity->id); 
    }

    /**
     * @return Default_Form_Login
     * 
     */ 
    public function getLoginForm() {
        $login_form = new Default_Form_Login; 
        return $login_form;
    }

    /**
     * @param string $action
     * 
     */ 
    public function logUserAction($action) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $server = Zend_Controller_Front::getInstance()
            ->getRequest()->getServer();
        $ip = (isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '');
        $user_actions = new Model_Mapper_UserActions;
        $user_actions->add($action, $ip, $identity->id);
    }
}
