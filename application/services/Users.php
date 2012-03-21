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
     * 
     * 
     */
    public function getUser() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_users->getById($identity->id);
    }

    /**
     * 
     * 
     */
    public function getProfile() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_user_profiles->getByUserId($identity->id);
    }
    
    /**
     * 
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
     * 
     * 
     */
    public function getSubscription() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $this->_user_subs->getByUserId($identity->id);
    }

    /**
     * 
     * 
     */
    public function updateProfile($data) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $this->_users->updatePersonal($data, $identity->id);
            $this->_user_profiles->updateByUserId($data, $identity->id);
            $auth_storage = Zend_Auth::getInstance()->getStorage();
            $auth_storage->write($this->getUser());
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack(); 
            throw new Exception($e->getMessage());
        }
    }

    public function updateLastLogin() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->_users->updateLastLogin($identity->id); 
    }

    /**
     * 
     * 
     */ 
    public function getLoginForm() {
        $login_form = new Default_Form_Login; 
        return $login_form;
    }
}
