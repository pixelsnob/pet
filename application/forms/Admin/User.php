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
     * @return void
     * 
     */
    public function init() {
        parent::init();
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
        $this->addSubform(new Form_SubForm_UserInfo, 'info');
        // Username
        $this->addElement('text', 'expiration', array(
            'label' => 'Expiration',
            'required' => false,
            'validators'   => array(
                array('Date', true, array(
                    'messages' => 'Invalid date'
                ))
            )));
    }
}
