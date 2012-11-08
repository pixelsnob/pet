<?php
/**
 * Magazine term select form
 * 
 */
class Form_SubscriptionTermSelect extends Pet_Form {
    
    /**
     * @var int
     * 
     */
    protected $_zone_id;

    /**
     * @param int
     * @return void
     */
    public function setZoneId($zone_id) {
        $this->_zone_id = $zone_id;
    }

    /**
     * @var int
     * 
     */
    protected $_is_gift;

    /**
     * @param int
     * @return void
     */
    public function setIsGift($is_gift) {
        $this->_is_gift = $is_gift;
    }

    /**
     * @var int
     * 
     */
    protected $_is_renewal;

    /**
     * @param int
     * @return void
     */
    public function setIsRenewal($is_renewal) {
        $this->_is_renewal = $is_renewal;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('radio', 'product_id', array(
            'label' => 'Subscriptions',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select a term'
                ))
            )
        ))->addElement('hidden', 'zone_id', array(
            'value' => $this->_zone_id
        ))->addElement('hidden', 'is_gift', array(
            'value' => $this->_is_gift
        ))->addElement('hidden', 'is_renewal', array(
            'value' => $this->_is_renewal
        ));
        
    }
}
