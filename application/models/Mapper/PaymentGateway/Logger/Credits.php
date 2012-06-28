<?php
/**
 * @package Model_Mapper_PaymentGateway_Logger
 * 
 * A snapshot of "set express checkout" requests
 * 
 */

class Model_Mapper_PaymentGateway_Logger_Credits extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
    }
    
    /**
     * @param bool $status
     * @param array $orig_payment
     * @param array $gateway_calls
     * @param array $exceptions
     * @return void
     * 
     */
    public function insert($status, array $orig_payment, array $gateway_calls,
                           array $exceptions = array()) {
        $mongo = Pet_Mongo::getInstance();
        $data = array(
            'timestamp'         => time(),
            'date_r'            => date('Y-m-d H:i:s'),
            'status'            => $status,
            'original_payment'  => $orig_payment,
            'gateway_calls'     => $gateway_calls,
            'exceptions'        => $exceptions
        );
        $mongo->payment_gateway_credits->ensureIndex('status');  
        $mongo->payment_gateway_credits->insert($data,
            array('fsync' => true));
    }
}
