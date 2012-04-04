<?php
/**
 * Magazine term select form
 * 
 */
class Default_Form_SubscriptionTermSelect extends Pet_Form {
    
    /**
     * @var array 
     * 
     */
    protected $_zones = array();
    
    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setZones(array $zones) {
        $this->_zones = $zones;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('susbscription_term_select');
        $this->addElement('radio', 'product_id', array(
            'label' => 'Subscriptions',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select a term'
                ))
            )
        ));
        
    }
}
