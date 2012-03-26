<?php
/**
 * Reset password form
 * 
 */
class Default_Form_ResetPassword extends Pet_Form {
    
    /**
     * @var int
     * 
     */
    protected $_user_id;

    /**
     * @param string $user_id
     * @return void
     */
    public function __construct($user_id) {
        parent::__construct();
        $this->_user_id = $user_id;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('reset_pw_form');
        $this->addElement('password', 'password', array(
            'label' => 'New Password',
            'id' => 'password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your new password'
                )),
                array('Callback', true, array(
                    'callback' => array($this, 'isNewPasswordValid'),
                    'messages' => 'New password must be different than old password'
                )),
                array(new Pet_Validate_PasswordStrength, true),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                ))
            )
        ))->addElement('password', 'confirm_password', array(
            'label' => 'Confirm Password',
            'id' => 'confirm-password',
            'required' => true,
            'renderPassword' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please confirm your password'
                )),
                array('StringLength', true, array(
                    'max' => 40,
                    'messages' => 'Password must be %max% characters or less'
                )),
                array('Identical', true, array(
                    'token' => 'password',
                    'messages' => 'Password and confirm password must be the same'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }

    /**
     * @param string $value
     * @param array $context
     * @return bool 
     * 
     */
    public function isNewPasswordValid($value, $context) {
        $users_svc = new Service_Users;
        $user = $users_svc->getUser($this->_user_id);
        if (!$user) {
            return false;
        }
        return $users_svc->validatePassword($user->password, trim($value)); 
    }
}
