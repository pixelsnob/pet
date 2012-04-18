<?php
/**
 * User profile form
 * 
 */
class Form_UserProfile extends Pet_Form {
    
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
     * @var array
     * 
     */
    protected $_countries;

    /**
     * @var array
     * 
     */
    protected $_states;

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
     * @param array $countries
     * @return void
     */
    public function setCountries(array $countries) {
        $this->_countries = $countries;
    }

    /**
     * @param array $countries
     * @return void
     */
    public function setStates(array $states) {
        $this->_states = $states;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $user_form = new Form_SubForm_User(array(
            'mapper' => $this->_mapper,
            'identity' => $this->_identity
        ));
        $this->addSubform($user_form, 'user');
        $billing_form = new Form_SubForm_Billing(array(
            'countries' => $this->_countries,
            'states'    => $this->_states
        ));
        $this->addSubform($billing_form, 'billing');
        $shipping_form = new Form_SubForm_Shipping(array(
            'countries' => $this->_countries,
            'states'    => $this->_states
        ));
        $this->addSubform($shipping_form, 'shipping');
        $this->addSubform(new Form_SubForm_UserInfo, 'info');
    }
}
