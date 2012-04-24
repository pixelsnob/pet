<?php
/**
 * @package Model_Cart_Billing
 * 
 */
class Model_Cart_Billing extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'billing_address'     => '',
        'billing_address_2'   => '',
        'billing_company'     => '',
        'billing_city'        => '',
        'billing_state'       => '',
        'billing_postal_code' => '',
        'billing_country'     => '',
        'billing_phone'       => ''
    );
}
