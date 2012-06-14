<?php
/**
 * @package Model_Report
 * 
 */
class Model_Report_Sale extends Pet_Model_Abstract {

    public $_data = array(
        'date_created' => null,
        'sku' => null,
        'total' => null,
        'email' => null,
        
        'billing_first_name' => null,
        'billing_last_name' => null,
        'billing_address' => null,
        'billing_address_2' => null,
        'billing_city' => null,
        'billing_state' => null,
        'billing_country' => null,
        'billing_postal_code' => null,
        
        'shipping_address' => null,
        'shipping_address_2' => null,
        'shipping_city' => null,
        'shipping_state' => null,
        'shipping_country' => null,
        'shipping_postal_code' => null,
        
        'previous_expiration' => null,
        
        'version' => null,
        'platform' => null,
        'marketing' => null,
        'occupation' => null
    );

}

