<?php
/**
 * User profile form
 * 
 */
class Default_Form_UserProfile extends Pet_Form {
    
    /**
     * @var Model_User 
     * 
     */
    protected $_identity;
    
    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_mapper;

    /**
     * @param Model_User $identity
     * @return void
     */
    public function setIdentity(Model_User $identity) {
        $this->_identity = $identity;
    }

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $user_form = new Default_Form_SubForm_User(array(
            'mapper' => $this->_mapper,
            'identity' => $this->_identity
        ));
        $this->addSubform($user_form, 'user');
        $this->addSubform(new Default_Form_SubForm_Billing, 'billing');
        $this->addSubform(new Default_Form_SubForm_Shipping, 'shipping');
        // Version
        $this->addElement('select', 'version', array(
            'label' => 'Version',
            'id' => 'version',
            'required' => false,
            'validators'   => array(
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Phone must be %max% characters or less'
                ))
            ),
            'multiOptions' => array(
                '' => 'Please select...',
                7  => 'Version 7',
                8  => 'Version 8',
                9  => 'Version 9',
                'other' => 'Other'
            )
        // Opt-in
        ))->addElement('checkbox', 'opt_in', array(
            'label' => 'Opt In',
            'id' => 'opt-in',
            'required' => false
        // Opt-in partner
        ))->addElement('checkbox', 'opt_in_partner', array(
            'label' => 'Opt In (Sponsors)',
            'id' => 'opt-in-partner',
            'required' => false
        ))->setElementFilters(array('StringTrim'));
    }
}
