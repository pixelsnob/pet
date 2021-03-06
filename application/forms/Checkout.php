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
     * @param array $states
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
        if (!$this->_cart->products->hasDigitalSubscription()
            && $this->_cart->products->hasSubscription() && !$this->_identity) {
            $validate_existing_email = false;
        }
        $user_form = new Form_SubForm_User(array(
            'mapper'                => $this->_users,
            'identity'              => $this->_identity,
            'validateExistingEmail' => ($this->_identity
                || $this->_cart->products->hasSubscription()
                || $this->_cart->products->hasDigitalSubscription())
        ));
        $promo_form = new Form_SubForm_Promo(array(
            'cart' => $this->_cart,
            'mapper' => $this->_promos
        ));
        $this->addSubform($promo_form, 'promo');
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
        $totals = $this->_cart->getTotals();
        if ($totals['total']) {
            $this->addSubform(new Form_SubForm_Payment, 'payment');
        }
        if ((!$this->_cart->products->hasDigitalSubscription() &&
                !$this->_cart->products->hasSubscription()) || $this->_identity) {
            $this->user->removeElement('password');
            $this->user->removeElement('confirm_password');
            $this->user->removeElement('username');
        }
        $this->addElement('checkbox', 'use_shipping', array(
            'label' => 'Check this box to enter a different delivery address',
            'required' => false,
            'validators'   => array(),
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND')),
                'Errors'
            ),
            'disableLoadDefaultDecorators' => true
        ));
    }

    /**
     * @param array $data
     * @return bool
     * 
     */
    public function isValid($data) {
        $valid = true;
        $valid = $this->billing->isValid($data) && $valid;
        if (!$this->_cart->isFreeOrder()) {
            $valid = $this->payment->isValid($data) && $valid;
        }
        $valid = $this->promo->isValid($data) && $valid;
        $valid = $this->user->isValid($data) && $valid;
        $valid = $this->info->isValid($data) && $valid;
        $use_shipping = (isset($data['use_shipping']) ?
            $data['use_shipping'] : false);
        if ($this->_cart->isShippingAddressRequired() && $use_shipping) {
            $valid = $this->shipping->isValid($data) && $valid;
        }       
        return $valid;
    }

    /**
     * @return array
     * 
     */
    public function getShippingValues() {
        if (!$this->_cart->use_shipping) {
            return array(
                'shipping_first_name'  => $this->user->first_name->getValue(),
                'shipping_last_name'   => $this->user->last_name->getValue(),
                'shipping_address'     => $this->billing->billing_address->getValue(),
                'shipping_address_2'   => $this->billing->billing_address_2->getValue(),
                'shipping_company'     => $this->billing->billing_company->getValue(),
                'shipping_city'        => $this->billing->billing_city->getValue(),
                'shipping_state'       => $this->billing->billing_state->getValue(),
                'shipping_postal_code' => $this->billing->billing_postal_code->getValue(),
                'shipping_country'     => $this->billing->billing_country->getValue(),
                'shipping_phone'       => $this->billing->billing_phone->getValue()
            );
        } else {
            return $this->shipping->getValues(true);
        }
    }
}
