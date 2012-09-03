<?php
/**
 * User profile form
 * 
 */
class Form_Admin_User extends Pet_Form {
    
    /**
     * @var Model_User 
     * 
     */
    protected $_identity;
    
    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_mapper;
    
    /**
     * @var string edit or add
     * 
     */
    protected $_mode = 'edit';

    /**
     * @param Model_User $identity
     * @return void
     */
    public function setIdentity(Model_User $identity) {
        $this->_identity = $identity;
    }

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param string $mode 
     * @return void
     */
    public function setMode($mode) {
        $this->_mode = $mode;
    }

    /**
     * @return string edit or add
     * 
     */
    public function getMode() {
        return $this->_mode;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setName('user_' . $this->_mode);
        $states = new Zend_Config(require APPLICATION_PATH .
            '/configs/states.php');
        $states = $states->toArray();
        $countries = new Zend_Config(require APPLICATION_PATH .
            '/configs/countries.php');
        $countries = $countries->toArray();
        $user_form = new Form_SubForm_User(array(
            'mapper' => $this->_mapper,
            'identity' => $this->_identity
        ));
        $this->addSubform($user_form, 'user');
        $this->user->username->setRequired(false);
        if ($this->_mode == 'edit') {
            $this->user->username->setAttrib('class', 'no-focus');
        }
        $billing_form = new Form_SubForm_Billing(array(
            'countries' => $countries,
            'states'    => $states
        ));
        $this->addSubform($billing_form, 'billing');
        $shipping_form = new Form_SubForm_Shipping(array(
            'countries' => $countries,
            'states'    => $states
        ));
        $this->addSubform($shipping_form, 'shipping');
        // Remove "not empty" validators
        foreach ($this->shipping as $el) {
            $el->removeValidator('NotEmpty')->setRequired(false);
        }
        $this->addSubform(new Form_SubForm_UserInfo, 'info');
        $this->addElement('text', 'expiration', array(
            'label' => 'Expiration',
            'required' => false,
            'class' => 'datepicker datepicker-min-today',
            'validators'   => array(
                array('Date', true, array(
                    'messages' => 'Invalid date'
                )),
                array(new Pet_Validate_DateNotBeforeToday, true)
            )
        ))->addElement('checkbox', 'is_active', array(
            'label' => 'Active',
            'required' => false,
            'class' => 'checkbox',
            'value' => 1
        ))->addElement('submit', 'submit', array(
            'label' => 'Update',
            'id'    => 'user-edit-submit',
            'name'  => 'user-edit-submit',
            'class' => 'submit'
        ));
        if ($this->_mode == 'edit') {
            $this->addElement('checkbox', 'change_password', array(
                'label' => 'Change Password',
                'required' => false,
                'class' => 'checkbox',
                'value' => 0
            ));
        }
    }

    /**
     * @param array $data
     * @return bool
     * 
     */
    public function isValid($data) {
        if (!isset($data['username']) || !strlen(trim($data['username']))) {
            $this->user->password->setRequired(false)->clearValidators();
            $this->user->confirm_password->setRequired(false)->clearValidators();
        }
        if ($this->_mode == 'edit' && (!isset($data['change_password']) ||
                $data['change_password'] == '0')) {
            $this->user->password->setRequired(false)->clearValidators();
            $this->user->confirm_password->setRequired(false)->clearValidators();
        }
        return parent::isValid($data);
    }


}
