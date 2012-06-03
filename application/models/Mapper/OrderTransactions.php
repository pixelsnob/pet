<?php
/**
 * @package Model_Mapper_OrderTransactions
 * 
 * A snapshot of orders/order attempts, payment gateway requests and responses, etc.
 * 
 */

class Model_Mapper_OrderTransactions extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
    }
    
    /**
     * @param bool $status
     * @param array $data
     * @param array $gateway_calls
     * @param array $exceptions
     * @return void
     * 
     */
    public function insert($status, array $data, array $gateway_calls,
                           array $exceptions = array()) {
        $mongo = Pet_Mongo::getInstance();
        $data = array_merge(array(
            'timestamp'        => time(),
            'date_r'           => date('Y-m-d H:i:s'),
            'status'           => $status,
        ), $data);
        $data = array_merge($data, array(
            'gateway_calls'    => $gateway_calls,
            'exceptions'       => $exceptions
        ));
        $mongo->order_transactions->insert($data, array('fsync' => true));
    }
}
