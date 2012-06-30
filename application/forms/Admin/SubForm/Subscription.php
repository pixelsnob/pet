<?php
/**
 * Admin subscription subform
 * 
 */
class Form_Admin_SubForm_Subscription extends Zend_Form_SubForm {
    
    /**
     * @var Model_Product
     * 
     */
    protected $_product;

    /**
     * @var array
     * 
     */
    protected $_subscription_zones;

    /**
     * @param Model_Product $product
     * @return void
     */
    public function setProduct($product) {
        $this->_product = $product;
    }

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
            'value'        => $this->_product->zone_id
        ))->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false,
            'value'        => $this->_product->description
        ))->addElement('text', 'term_months', array(
            'label'        => 'Term (months)',
            'required'     => true,
            'value'        => $this->_product->term_months
        ))->addElement('checkbox', 'is_renewal', array(
            'label'        => 'Renewal?',
            'class'        => 'checkbox',
            'required'     => false,
            'value'        => $this->_product->is_renewal
        ));

    }

}
