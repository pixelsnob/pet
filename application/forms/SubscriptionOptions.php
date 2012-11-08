<?php
/**
 * Subscription options form
 * 
 */
class Form_SubscriptionOptions extends Pet_Form {
    
    /**
     * @var array
     * 
     */
    protected $_subscriptions;
    
    /**
     * @param array $subscriptions
     * @return void
     */
    public function setSubscriptions(array $subscriptions) {
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
        ))->addElement('hidden', 'zone_id')
          ->addElement('hidden', 'is_gift')
          ->addElement('hidden', 'is_renewal')
          ->addElement('hidden', 'term')
          ->addElement('hidden', 'promo_code');
        
    }
}
