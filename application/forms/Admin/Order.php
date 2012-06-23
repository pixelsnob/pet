<?php
/**
 * Order form, currently only handles one product
 * 
 */
class Form_Admin_Order extends Pet_Form {
    
    /**
     * @var Model_User 
     * 
     */
    //protected $_identity;
    
    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_mapper;

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
     * @param Model_User $identity
     * @return void
     */
    /*public function setIdentity(Model_User $identity) {
        $this->_identity = $identity;
    }*/

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_mapper = $mapper;
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
            'mapper' => $this->_mapper,
            'identity' => $this->_identity
        ));
        $this->addSubform($user_form, 'user');
        $this->user->addPasswordFields();
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
            //'cart' => $this->_cart,
            //'mapper' => $this->_promos
        ));
        $this->addSubform($promo_form, 'promo');
        $this->addSubform(new Form_SubForm_Payment, 'payment');
        $this->addElement('select', 'product', array(
            'label'        => 'Choose a Product',
            'multiOptions' => array(
                'Subscriptions'          => $subscriptions,
                'Digital Subscriptions'  => $digital_subscriptions,
            )
        ))->addElement('submit', 'submit', array(
            'label' => 'Update',
            'class' => 'submit'
        ));
    }
}
