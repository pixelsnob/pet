<?php
/**
 * Subscription options form
 * 
 */
class Form_SubscriptionOptions extends Pet_Form {
    
    /**
     * @var int
     * 
     */
    protected $_zone_id;

    /**
     * @var int
     * 
     */
    protected $_is_gift;

    /**
     * @var int
     * 
     */
    protected $_is_renewal;

    /**
     * @var string
     * 
     */
    protected $_promo_code;

    /**
     * @var array
     * 
     */
    protected $_subscriptions;

    /**
     * @param int
     * @return void
     */
    public function setZoneId($zone_id) {
        $this->_zone_id = $zone_id;
    }

    /**
     * @param int
     * @return void
     */
    public function setIsGift($is_gift) {
        $this->_is_gift = $is_gift;
    }

    /**
     * @param int
     * @return void
     */
    public function setIsRenewal($is_renewal) {
        $this->_is_renewal = $is_renewal;
    }

    /**
     * @param string
     * @return void
     */
    public function setPromoCode($promo_code) {
        $this->_promo_code = $promo_code;
    }

    /**
     * @param array
     * @return void
     */
    public function setSubscriptions($subscriptions) {
        $this->_subscriptions = $subscriptions;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $sub_opts = array();
        foreach ($this->_subscriptions as $sub) {
            $sub_opts[$sub->product_id] = $sub->name;
        }   
        $this->addElement('radio', 'product_id', array(
            'label' => 'Subscriptions',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select a subscription'
                ))
            ),
            'multiOptions' => $sub_opts
        ))->addElement('hidden', 'zone_id', array(
            'value' => $this->_zone_id
        ))->addElement('hidden', 'is_gift', array(
            'value' => $this->_is_gift
        ))->addElement('hidden', 'is_renewal', array(
            'value' => $this->_is_renewal
        ))->addElement('hidden', 'promo_code', array(
            'value' => $this->_promo_code
        ));
        
    }
}
