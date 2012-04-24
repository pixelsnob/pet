<?php
/**
 * Checkout form
 * 
 */
class Form_Checkout extends Pet_Form {
    
    /**
     * @var Model_User 
     * 
     */
    protected $_identity;
    
    /**
     * @var Model_Mapper_Users
     * 
     */
    protected $_users;

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
     * @var Model_Cart
     * 
     */
    protected $_cart;

    /**
     * @var Model_Mapper_Promos
     * 
     */
    protected $_promos;
    
    
    /**
     * @param mixed $identity
     * @return void
     */
    public function setIdentity($identity) {
        $this->_identity = $identity;
    }

    /**
     * @param Model_Mapper_Users $mapper
     * @return void
     */
    public function setUsers(Model_Mapper_Users $users_mapper) {
        $this->_users = $users_mapper;
    }

    /**
     * @param Model_Mapper_Promos $mapper
     * @return void
     */
    public function setPromos(Model_Mapper_Promos $promos_mapper) {
        $this->_promos = $promos_mapper;
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
     * @param Model_Cart $cart
     * @return void
     * 
     */
    public function setCart(Model_Cart $cart) {
        $this->_cart = $cart;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $user_form = new Form_SubForm_User(array(
            'mapper' => $this->_users,
            'identity' => $this->_identity
        ));
        $user_form->addPasswordFields();
        $promo_form = new Form_SubForm_Promo(array(
            'cart' => $this->_cart,
            'mapper' => $this->_promos
        ));
        $this->addSubform($promo_form, 'promo');
        $this->addSubform($user_form, 'user');
        $this->addSubform(new Form_SubForm_Billing, 'billing');
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
        $this->addSubform(new Form_SubForm_Payment, 'payment');
    }
}
