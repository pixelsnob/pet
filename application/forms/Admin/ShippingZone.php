<?php
/**
 * Shipping zone form
 * 
 */
class Form_Admin_ShippingZone extends Pet_Form {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setName('shipping_zone_edit');
        $this->addElement('text', 'usa', array(
            'label'        => 'USA',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'USA amount is required'
                )),
                array(new Pet_Validate_Currency, true),
                array('LessThan', true, array(
                    'max' => 1000,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->addElement('text', 'can', array(
            'label'        => 'Canada',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Canada amount is required'
                )),
                array(new Pet_Validate_Currency, true),
                array('LessThan', true, array(
                    'max' => 1000,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->addElement('text', 'intl', array(
            'label'        => 'International',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'International amount is required'
                )),
                array(new Pet_Validate_Currency, true),
                array('LessThan', true, array(
                    'max' => 1000,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }
    

}
