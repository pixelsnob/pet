<?php
/**
 * Users service layer
 *
 * @package Service_Users
 * 
 */

require_once 'TokenGenerator.php';

class Service_Users extends Pet_Service {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_user_profiles = new Model_Mapper_UserProfiles;
        $this->_user_actions = new Model_Mapper_UserActions;
        $this->_users = new Model_Mapper_Users;
    }

    /**
     * @param array $data Username and password, etc.
     * @param bool $is_superuser 
     * @return bool Auth status
     * 
     * 
     */
    public function login($data, $is_superuser = false) {
        $config = Zend_Registry::get('app_config');
        $username = (isset($data['username']) ? $data['username'] : '');
        $password = (isset($data['password']) ? $data['password'] : '');
        $auth_session = new Zend_Session_Namespace('Zend_Auth');
        $auth_session->timestamp = time();
        $auth_adapter = new Pet_Auth_Adapter($username, $password, $is_superuser);
        $auth = Zend_Auth::getInstance();
        Zend_Session::regenerateId();
        return $auth->authenticate($auth_adapter)->isValid();
    }
    
    /**
     * @return void
     * 
     */
    public function logout() {
        $this->logUserAction('User logged out');
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::regenerateId();
    }

    /**
     * @param bool $is_superuser
     * @return bool Auth status
     * 
     */
    public function isAuthenticated($is_superuser = false) {
        if ($identity = Zend_Auth::getInstance()->getIdentity()) {
            if ($is_superuser && !$identity->is_superuser) { 
                false;
            }
            $config = Zend_Registry::get('app_config');
            $auth_session = new Zend_Session_Namespace('Zend_Auth');
            $ts = (int) $auth_session->timestamp;
            if ($ts && (time() - $ts > $config['user_session_timeout'])) {
                $this->logUserAction('User timed out');
                Zend_Auth::getInstance()->clearIdentity();
                return false;
            }
            $auth_session->timestamp = time();
            return true;
        }
        return false;
    }
    
    /**
     * @param int|null $user_id
     * @return Model_User 
     * 
     */
    public function getUser($user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        if (!$user_id) {
            return;
        }
        return $this->_users->getById($user_id);
    }

    /**
     * @param string $username
     * @param bool $is_superuser
     * @return Model_User
     * 
     */
    public function getActiveUserByUsername($username, $is_superuser = false) {
        return $this->_users->getActiveByUsername($username, $is_superuser); 
    }
    
    /**
     * @return Model_User
     * 
     */
    public function getActiveUserByEmail($email) {
        return $this->_users->getActiveByEmail($email); 
    }

    /**
     * Returns id of logged in user
     * 
     * @return null|int User id
     *  
     */
    public function getId() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        return (isset($identity->id) ? $identity->id : null);
    }

    /**
     * @param null|int $user_id
     * @return Model_UserProfile 
     * 
     */
    public function getProfile($user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        if (!$user_id) {
            return;
        }
        return $this->_user_profiles->getByUserId($user_id);
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
     * @return Form_Login
     * 
     */ 
    public function getLoginForm($redirect_to = null, $redirect_params = array()) {
        $login_form = new Form_Login(array(
            'redirectTo'     => $redirect_to,
            'redirectParams' => $redirect_params
        ));
        return $login_form;
    }

    /**
     * @return bool|Form_UserProfile
     * 
     */
    public function getProfileForm() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$identity) {
            return false;
        }
        $user = $this->getUser();
        $profile = $this->getProfile();
        if ($user && $profile) {
            $states = new Zend_Config(require APPLICATION_PATH .
                '/configs/states.php');
            $countries = new Zend_Config(require APPLICATION_PATH .
                '/configs/countries.php');
            $profile_form = new Form_UserProfile(array(
                'identity'  => $identity,
                'mapper'    => $this->_users,
                'states'    => $states->toArray(),
                'countries' => $countries->toArray()
            ));
            $form_data = array_merge($user->toArray(), $profile->toArray());
            $profile_form->populate($form_data);
            return $profile_form;
        }
        return false;
    }
    
    /**
     * @return Form_ResetPasswordRequest
     * 
     */
    public function getResetPasswordRequestForm() {
        return new Form_ResetPasswordRequest;    
        return $form;
    }
    
    /**
     * @param null|int $user_id
     * @return Form_ResetPassword 
     * 
     */
    public function getResetPasswordForm($user_id = null) {
        $user = $this->getUser($user_id);
        return new Form_ResetPassword(array('user' => $user));
    }
    
    /**
     * @return Form_ChangePassword
     * 
     */ 
    public function getChangePasswordForm() {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $login_form = new Form_ChangePassword(array(
            'user' => $identity)); 
        return $login_form;
    }    
    
    /**
     * @param mixed $user_id
     * @param mixed $digital_only
     * @return null|Model_OrderProductSubscription
     * 
     */
    public function getExpirations($user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $regular_sub = $ops_mapper->getUnexpiredByUserId($user_id, false);
        $digital_sub = $ops_mapper->getUnexpiredByUserId($user_id, true);
        $out = new StdClass;
        $out->regular = null;
        $out->digital = null;
        if ($regular_sub) {
            $out->regular = $regular_sub->expiration;
        }
        // Regular sub also includes digital access: if digital expiration 
        // does not exist, or if it exists and it's an earlier expiration
        // date than the regular sub, use the regular sub.
        if ($digital_sub && !$regular_sub) {
            $out->digital = $digital_sub->expiration;
        } elseif (!$digital_sub && $regular_sub) {
            $out->digital = $regular_sub->expiration;
        } elseif ($digital_sub && $regular_sub) {
            $date_r = new DateTime($regular_sub->expiration);
            $date_d = new DateTime($digital_sub->expiration);
            $date_interval = $date_d->diff($date_r);
            if ($date_interval->days == 0 || $date_interval->invert) {
                // Digital sub has greater expiration date
                $out->digital = $digital_sub->expiration;
            } else {
                // Regular sub has greater expiration date
                $out->digital = $regular_sub->expiration;
            }
        }
        return $out; 
    }

    /**
     * @param array $data
     * @param null|int $user_id
     * @return void
     * 
     */
    public function updateProfile(array $data, $user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $this->_users->updatePersonal($data, $user_id);
        $this->_user_profiles->updateByUserId($data, $user_id);
        $this->logUserAction('Profile updated', $user_id);
        $db->commit();
        $auth_storage = Zend_Auth::getInstance()->getStorage();
        $auth_storage->write($this->getUser());
        Zend_Session::regenerateId();
        session_write_close();
    }
    
    /**
     * @param null|int $user_id
     * @return void
     * 
     */
    public function updateLastLogin($user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        $this->_users->updateLastLogin($user_id); 
    }

    /**
     * @param string $new_pw
     * @param null|int $user_id
     * @return void
     * 
     */
    public function updatePassword($new_pw, $user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        $enc_pw = $this->generateHash($new_pw); 
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $this->_users->updatePassword($enc_pw, $user_id);
        $this->logUserAction('Password updated', $user_id);
        $db->commit();
        $auth_storage = Zend_Auth::getInstance()->getStorage();
        $auth_storage->write($this->getUser());
        Zend_Session::regenerateId();
        session_write_close();
    }

    /**
     * @param string $action
     * @param null|int $user_id
     * 
     */ 
    public function logUserAction($action, $user_id = null) {
        if (!$user_id) {
            $user_id = $this->getId();
        }
        $server = Zend_Controller_Front::getInstance()
            ->getRequest()->getServer();
        $ip = (isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '');
        $user_actions = new Model_Mapper_UserActions;
        try {
            $user_actions->insert($action, $ip, $user_id);
        } catch (Exception $e) {}
    }


    /**
     * Passwords are stored as sha1$salt$hash
     * 
     * @param string $hash
     * @param string $pw
     * 
     */
    static public function validatePassword($hash, $value) {
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
     * Generates a token, and sends a link via email
     * 
     * @param Model_User $user
     * @return void
     * 
     */
    public function resetPasswordRequest(Model_User $user) {
        $token_gen = new TokenGenerator;
        $token = $token_gen->generate();
        $pw_tokens = new Model_Mapper_UserPasswordTokens; 
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $pw_tokens->deleteByUserId($user->id);
        $pw_tokens->add($user->id, $token);
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getResource('view'); 
        $view->token = $token;
        $view->user = $user;
        $message = $view->render('profile/reset-password-email.phtml');
        $mail = new Zend_Mail;
        $mail->setBodyText($message)
            ->addTo($user->email)
            ->setSubject('Photoshop Elements User Password Reset');
        $mail->send();
        $log_msg = "Password reset email sent to {$user->email}, token $token";
        $this->logUserAction($log_msg, $user->id);
        $db->commit();
    }
    
    /**
     * @param string $new_pw
     * @param string $token
     * @return void
     * 
     */
    public function resetPasswordByToken($new_pw, $token) {
        $pw_tokens = new Model_Mapper_UserPasswordTokens; 
        $enc_pw = $this->generateHash($new_pw); 
        $db = Zend_Db_Table::getDefaultAdapter();
        $token = $pw_tokens->getByToken($token);
        $db->beginTransaction();
        $this->_users->updatePassword($enc_pw, $token->user_id);
        $pw_tokens->deleteByUserId($token->user_id); 
        $this->logUserAction('Password reset', $token->user_id);
        $db->commit();
    }

    /**
     * Passwords are stored as sha1$salt$hash
     * 
     * @param $new_pw
     * @return string
     * 
     */
    public function generateHash($new_pw) {
        $token_gen = new TokenGenerator;
        return $token_gen->generateHash($new_pw); 
    }
}
