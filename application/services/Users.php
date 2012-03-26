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
        return $this->_users->getById($this->getId());
    }
    
    /**
     * @return Model_User
     * 
     */
    public function getActiveUserByEmail($email) {
        return $this->_users->getActiveByEmail($email); 
    }

    /**
     * @return int User id
     * 
     */
    public function getId() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return $identity->id;
    }

    /**
     * @return Model_UserProfile 
     * 
     */
    public function getProfile() {
        return $this->_user_profiles->getByUserId($this->getId());
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
        return $this->_user_subs->getByUserId($this->getId());
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function updateProfile(array $data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $this->_users->updatePersonal($data, $this->getId());
        $this->_user_profiles->updateByUserId($data, $this->getId());
        $this->logUserAction('Profile updated');
        $db->commit();
        $auth_storage = Zend_Auth::getInstance()->getStorage();
        $auth_storage->write($this->getUser());
        Zend_Session::regenerateId();
        session_write_close();
    }
    
    /**
     * @return void
     * 
     */
    public function updateLastLogin() {
        $this->_users->updateLastLogin($this->getId()); 
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
        $server = Zend_Controller_Front::getInstance()
            ->getRequest()->getServer();
        $ip = (isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '');
        $user_actions = new Model_Mapper_UserActions;
        $user_actions->add($action, $ip, $this->getId());
    }

    /**
     * @return Default_Form_ChangePassword
     * 
     */ 
    public function getChangePasswordForm() {
        $login_form = new Default_Form_ChangePassword; 
        return $login_form;
    }
    
    /**
     * @param string $new_pw
     * @return void
     * 
     */
    public function updatePassword($new_pw) {
        $enc_pw = $this->_generateHash($new_pw); 
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $this->_users->updatePassword($enc_pw, $this->getId());
        $this->logUserAction('Password updated');
        $db->commit();
        $auth_storage = Zend_Auth::getInstance()->getStorage();
        $auth_storage->write($this->getUser());
        Zend_Session::regenerateId();
        session_write_close();
    }

    /**
     * @param string $new_pw
     * @param string $token
     * @return void
     * 
     */
    public function resetPassword($new_pw, $token) {
        $pw_tokens = new Model_Mapper_UserPasswordTokens; 
        $enc_pw = $this->_generateHash($new_pw); 
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $token = $pw_tokens->getByToken($token);
        print_r($token);
        exit;

        $db->beginTransaction();
        $this->_users->updatePassword($enc_pw, $this->getId());
        $pw_tokens->deleteByUserId($this->getId()); 
        $this->logUserAction('Password reset');
        $db->commit();
        $auth_storage = Zend_Auth::getInstance()->getStorage();
        $auth_storage->write($this->getUser());
        Zend_Session::regenerateId();
        session_write_close();
    }

    /**
     * Passwords are stored as sha1$salt$hash
     * 
     * @param string $hash
     * @param string $pw
     * 
     */
    public function validatePassword($hash, $value) {
        $pw = explode('$', $hash);
        if (count($pw) == 3) {
            $hash = sha1($pw[1] . $value);
            if ($hash == $pw[2]) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * 
     */
    public function getResetPasswordRequestForm() {
        $form = new Default_Form_ResetPasswordRequest;    
        return $form;
    }
    
    /**
     * 
     * 
     */
    public function resetPasswordRequest(Model_User $user) {
        // Build token
        $token = '';
        for ($i = 0; $i < 32; $i++) { 
            $token .= chr(mt_rand(0, 255));
        }
        // ZF's routing doesn't like encoded slashes
        $token = str_replace('/', '', base64_encode($token));
        $pw_tokens = new Model_Mapper_UserPasswordTokens; 
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $pw_tokens->deleteByUserId($user->id);
        $pw_tokens->add($user->id, $token);
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getResource('view'); 
        $view->token = $token;
        $view->user = $user;
        $message = $view->render('subscribe/reset-password-email.phtml');
        $mail = new Zend_Mail;
        $mail->setBodyText($message)
            ->addTo($user->email)
            ->setSubject('Photoshop Elements User Password Reset');
            //->addBcc('');
        $mail->send();
        $db->commit();
    }
    
    /**
     * @param string $token
     * @return void|Model_UserPasswordToken
     * 
     */
    public function getValidPasswordResetToken($token) {
        $pw_tokens = new Model_Mapper_UserPasswordTokens; 
        return $pw_tokens->getByMaxAge($token, 1800);
    }

    /**
     * 
     * 
     */
    public function getResetPasswordForm() {
        $form = new Default_Form_ResetPassword;
        return $form;
    }
    
    /**
     * Passwords are stored as sha1$salt$hash
     * 
     * @param $new_pw
     * @return string
     * 
     */
    protected function _generateHash($new_pw) {
        $salt = '';
        for ($i = 0; $i < 5; $i++) { 
            $salt .= chr(mt_rand(0, 255));
        }
        $salt = substr(sha1($salt), 0, 5);
        $hash = sha1($salt . $new_pw);
        return ('sha1$' . $salt . '$' . $hash);
    }
}
