<?php
/**
 * Cart_Event model, for logging cart events
 * 
 */
class Model_Cart_Event extends Onone_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        '_id'           => null,
        'date'          => null,
        'date_r'        => '', 
        'type'          => '',
        'status'        => '',
        'exceptions'    => array(),
        'cart'          => array(),
        'gateway_calls' => array(),
        'order_id'      => 0,
        'user_id'       => 0,
        'ec_token'      => '',
        'ec_payer_id'   => '',
        'exact_target'  => array(),
        'server'        => array()
    );
}
