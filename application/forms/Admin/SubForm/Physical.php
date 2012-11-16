<?php
/**
 * Admin physical product subform
 * 
 */
class Form_Admin_SubForm_Physical extends Zend_Form_SubForm {
    
    /**
     * @var array
     * 
     */
    protected $_shipping_zones;

    /**
     * @param array
     * @return void
     */
    public function setShippingZones(array $zones) {
        $this->_shipping_zones = $zones;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $zones = array('' => 'Please select...');
        foreach ($this->_shipping_zones as $zone) {
            $zones[$zone->id] = $zone->getLabel();
        }
        $this->addElement('select', 'shipping_zone_id', array(
            'label'        => 'Shipping Zone',
            'required'     => true,
            'multiOptions' => $zones,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Shipping zone is required'
                ))
            )
        ));
    }

}
