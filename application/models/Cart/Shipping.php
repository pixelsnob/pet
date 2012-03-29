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
        'ship_first_nm'       => '',
        'ship_last_nm'        => '',
        'ship_company_nm'     => '',
        'ship_addr1'          => '',
        'ship_addr2'          => '',
        'ship_city'           => '',
        'ship_state'          => '',
        'ship_zip'            => '',
        'ship_phone'          => '',
        'ship_country'        => '',
        'shipping_method'     => ''
    );
}
