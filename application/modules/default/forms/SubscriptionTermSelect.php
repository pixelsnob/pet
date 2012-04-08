<?php
/**
 * Magazine term select form
 * 
 */
class Default_Form_SubscriptionTermSelect extends Pet_Form {
    
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
    protected $_gift;

    /**
     * @param int
     * @return void
     */
    public function setGift($gift) {
        $this->_gift = $gift;
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
        ))->addElement('hidden', 'gift', array(
            'value' => $this->_gift
        ));
        
    }
}
