<?php
/**
 * Order form, currently only handles one product
 * 
 */
class Form_Admin_Order extends Pet_Form {
    
    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_users_mapper;

    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_promos_mapper;

    /**
     * @var array
     * 
     */
    protected $_subscriptions;

    /**
     * @var array
     * 
     */
    protected $_digitalSubscriptions;

    /**
     * @var Model_Cart
     * 
     */
    protected $_cart;

    /**
     * @var int
     * 
     */
    protected $_user_id;

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setUsersMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_users_mapper = $mapper;
    }

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setPromosMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_promos_mapper = $mapper;
    }


    /**
     * @param array An array of Model_Product_Subscription objects
     * @return void
     */
    public function setSubscriptions(array $subs) {
        $this->_subscriptions = $subs;
    }

    /**
     * @param array An array of Model_Product_DigitalSubscription objects
     * @return void
     */
    public function setDigitalSubscriptions(array $subs) {
        $this->_digitalSubscriptions = $subs;
    }

    /**
     * @param Model_Cart $cart
     * @return void
     */
    public function setCart(Model_Cart $cart) {
        $this->_cart = $cart;
    }

    /**
     * @param Model_Cart $cart
     * @return void
     */
    public function setUserId($user_id) {
        $this->_user_id = $user_id;
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
        $subscriptions = array();
        foreach ($this->_subscriptions as $sub) {
            $subscriptions[$sub->product_id] = $sub->name;
        }
        $digital_subscriptions = array();
        foreach ($this->_digitalSubscriptions as $digsub) {
            $digital_subscriptions[$digsub->product_id] = $digsub->name;
        }
        $user_form = new Form_SubForm_User(array(
            'mapper' => $this->_users_mapper,
            //'identity' => $this->_identity
        ));
        $this->addSubform($user_form, 'user');
        $this->user->username->setRequired(false);
        if ($this->_user_id) {
            $this->user->removeElement('username');
            $this->user->removeElement('password');
            $this->user->removeElement('confirm_password');
            $this->user->removeElement('email');
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
        $promo_form = new Form_SubForm_Promo(array(
            'cart'   => $this->_cart,
            'mapper' => $this->_promos_mapper
        ));
        $this->addSubform($promo_form, 'promo');
        $this->promo->promo_code->setLabel('Promo code')->clearValidators();
        $this->addSubform(new Form_Admin_SubForm_Payment, 'payment');
        $this->addElement('select', 'product', array(
            'label'        => 'Choose a Product',
            'required'     => true,
            'multiOptions' => array(
                ''                       => 'Please select...',
                'Subscriptions'          => $subscriptions,
                'Digital Subscriptions'  => $digital_subscriptions,
            ),
            'validators' => array(array('NotEmpty', true, array(
                'messages' => 'Select a product'
            )))
        ))->addElement('submit', 'submit', array(
            'label' => 'Update',
            'class' => 'submit'
        ));
    }

    /**
     * @param array $data
     * @return bool
     * 
     */
    public function isValid($data) {
        $valid = true;
        if (!$this->_user_id && (!isset($data['username']) || !strlen(trim($data['username'])))) {
            $this->user->password->setRequired(false)->clearValidators();
            $this->user->confirm_password->setRequired(false)->clearValidators();
        }
        return parent::isValid($data);
    }
}
