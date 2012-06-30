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
            'multiOptions' => $zones
        ))->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false
        ))->addElement('text', 'term_months', array(
            'label'        => 'Term (months)',
            'required'     => true
        ))->addElement('checkbox', 'is_renewal', array(
            'label'        => 'Renewal?',
            'class'        => 'checkbox',
            'required'     => false
        ));

    }

}
