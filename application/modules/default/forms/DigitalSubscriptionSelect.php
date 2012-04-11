<?php
/**
 * Digital subscription select form
 * 
 */
class Default_Form_DigitalSubscriptionSelect extends Pet_Form {
    
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
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('digital_subscription_select');
        $this->addElement('radio', 'product_id', array(
            'label' => 'Subscriptions',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select an option'
                ))
            )
        ))->addElement('hidden', 'is_gift', array(
            'value' => $this->_is_gift
        ));
        
    }
}
