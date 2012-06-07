<?php
/**
 * Used to pass around data at checkout
 * 
 */
class Model_Cart_Order extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        //'name' => null,
        //'address' => null,
        'billing_address' => null,
        'billing_address_2' => null,
        'billing_company' => null,
        'billing_city' => null,
        'billing_state' => null,
        'billing_postal_code' => null,
        'billing_country' => null,
        'billing_phone' => null,
        'payment_method' => null,
        'cc_num' => null,
        'cc_exp_month' => null,
        'cc_exp_year' => null,
        'cc_exp' => null,
        'cc_cvv' => null,
        'subtotal' => null,
        'discount' => null,
        'total' => null,
        'username' => null,
        'email' => null,
        'first_name' => null,
        'last_name' => null,
        'password' => null,
        'confirm_password' => null,
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
        'version' => null,
        'opt_in' => null,
        'opt_in_partner' => null,
        'promo_id' => null,
        'old_expiration' => null,
        'user_id' => null,
        'order_id' => null,
        'products' => array()
    );

}
