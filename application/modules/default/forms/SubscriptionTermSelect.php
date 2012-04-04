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
        $this->addElement('radio', 'subscriptions', array(
            'label' => 'Subscription',
            'id' => 'subscription_id',
            'required' => true/*,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter your username'
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => 'Username must be %max% characters or less'
                )),

                array(new Pet_Validate_UsernameNotExists(
                    $this->_identity, $this->_mapper), true),
                array('Alnum', true, array(
                    'messages' => 'Only letters and numbers allowed'
                ))
            )*/
        ));
        
    }
}
