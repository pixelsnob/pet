<?php
/**
 * @package Model_Mapper_PaymentGateway_Logger
 * 
 * A snapshot of recurring billing transactions
 * 
 */

class Model_Mapper_PaymentGateway_Logger_RecurringBilling extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
    }
    
    /**
     * @param bool $status
     * @param array $order
     * @param array $gateway_calls
     * @param array $exceptions
     * @return void
     * 
     */
    public function insert($status, array $order, array $gateway_calls,
                           array $exceptions = array()) {
        $mongo = Pet_Mongo::getInstance();
        $data = array(
            'timestamp'         => time(),
            'date_r'            => date('Y-m-d H:i:s'),
            'status'            => $status,
            'order'             => $order,
            'gateway_calls'     => $gateway_calls,
            'exceptions'        => $exceptions
        );
        $mongo->payment_gateway_recurring_billing->ensureIndex('status');  
        $mongo->payment_gateway_recurring_billing->insert($data,
            array('fsync' => true));
    }
}
