<?php
/**
 * Admin subscription subform
 * 
 */
class Form_Admin_SubForm_Subscription extends Zend_Form_SubForm {
    
    /**
     * @var array
     * 
     */
    protected $_subscription_zones;

    /**
     * @param array
     * @return void
     */
    public function setSubscriptionZones(array $zones) {
        $this->_subscription_zones = $zones;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $zones = array('' => 'Please select...');
        foreach ($this->_subscription_zones as $zone) {
            $zones[$zone->id] = $zone->name;
        }
        $this->addElement('select', 'zone_id', array(
            'label'        => 'Subscription Zone',
            'required'     => true,
            'multiOptions' => $zones,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Subscription zone is required'
                ))
            )
        ))->addElement('text', 'term_months', array(
            'label'        => 'Term (months)',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Term is required'
                )),
                array('Digits', true, array(
                    'messages' => 'Please enter a positive number'
                )),
                array('LessThan', true, array(
                    'max'      => 360,
                    'messages' => 'Term must be less than %max%'
                ))
            )
        ))->addElement('checkbox', 'is_renewal', array(
            'label'        => 'Renewal?',
            'class'        => 'checkbox',
            'required'     => false
        ));

    }

}
