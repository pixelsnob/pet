<?php
/**
 * @package Model_Order
 * 
 */
class Model_Order extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'user_id' => null,
        'promo_id' => null,
        'date_created' => null,
        'date_updated' => null,
        'email' => null,
        'billing_first_name' => null,
        'billing_last_name' => null,
        'billing_address' => null,
        'billing_address_2' => null,
        'billing_company' => null,
        'billing_city' => null,
        'billing_country' => null,
        'billing_state' => null,
        'billing_postal_code' => null,
        'billing_phone' => null,
        'shipping_first_name' => null,
        'shipping_last_name' => null,
        'shipping_address' => null,
        'shipping_address_2' => null,
        'shipping_company' => null,
        'shipping_city' => null,
        'shipping_state' => null,
        'shipping_postal_code' => null,
        'shipping_country' => null,
        'shipping_phone' => null,
        'shipping_cost' => 0,
        'total_cost' => 0,
        'phone_order' => 0,
        'active' => 1
    );
}
