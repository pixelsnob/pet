<?php
/**
 * @package Model_Cart_Shipping
 * 
 */
class Model_Cart_Shipping extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'shipping_first_name'  => '',
        'shipping_last_name'   => '',
        'shipping_address'     => '',
        'shipping_address_2'   => '',
        'shipping_company'     => '',
        'shipping_city'        => '',
        'shipping_state'       => '',
        'shipping_postal_code' => '',
        'shipping_country'     => '',
        'shipping_phone'       => ''
    );
}
