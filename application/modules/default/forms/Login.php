<?php
/**
 * Login form
 * 
 */
class Default_Form_Login extends Pet_Form {
    
    /**
     * @var string
     * 
     */
    protected $_redirect_to;
    
    /**
     * @param string
     * @return void
     */
    public function setRedirectTo($redirect_to) {
        $this->_redirect_to = $redirect_to;
    }

    /**
     * @var string
     * 
     */
    protected $_redirect_params;
    
    /**
     * @param string
     * @return void
     */
    public function setRedirectParams(array $redirect_params) {
        $this->_redirect_params = $redirect_params;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'id' => 'login-username',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username'
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
        }
        if ($this->_redirect_to && !empty($this->_redirect_params)) {
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
    }
}
