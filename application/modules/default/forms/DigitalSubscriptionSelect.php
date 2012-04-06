<?php
/**
 * Digital subscription select form
 * 
 */
class Default_Form_DigitalSubscriptionSelect extends Pet_Form {
    
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
                    'messages' => 'Please select am option'
                ))
            )
        ));
        
    }
}
