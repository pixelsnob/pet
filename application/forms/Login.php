<?php
/**
 * Login form
 * 
 */
class Form_Login extends Pet_Form {
    
    /**
     * @var string
     * 
     */
    protected $_redirect_to;

    /**
     * @var array
     * 
     */
    protected $_redirect_params = array();

    /**
     * @var string
     * 
     */
    protected $_redirect_url;
    
    /**
     * @param string
     * @return void
     */
    public function setRedirectTo($redirect_to) {
        $this->_redirect_to = $redirect_to;
    }
    
    /**
     * @param array
     * @return void
     */
    public function setRedirectParams(array $redirect_params) {
        $this->_redirect_params = $redirect_params;
    }

    /**
     * @param string
     * @return void
     */
    public function setRedirectUrl($redirect_url) {
        $this->_redirect_url = $redirect_url;
    }


    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('text', 'username', array(
            'label' => 'Username or Email',
            'id' => 'login-username',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username or email'
                ))
            )
        ))->addElement('password', 'password', array(
            'label' => 'Password',
            'id' => 'login-password',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your password'
                ))
            )
        ));
        if ($this->_redirect_to) {
            $this->addElement('hidden', 'redirect_to', array(
                'value' => $this->_redirect_to
            ));
            if (!empty($this->_redirect_params)) {
                $redirects = new Zend_Form;
                $redirects->setDecorators(array('FormElements'));
                $this->addSubForm($redirects, 'redirect_params');
                foreach ($this->_redirect_params as $k => $v) {
                    $redirects->addElement('hidden', $k, array(
                        'value'     => $v,
                        'belongsTo' => 'redirect_params',
                        'decorators' => array(
                            'ViewHelper'
                        )
                    ));
                }
            }
        } elseif ($this->_redirect_url) {
            $this->addElement('hidden', 'redirect_url', array(
                'value' => $this->_redirect_url
            ));
        }
    }
}
