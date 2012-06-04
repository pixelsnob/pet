<?php
/**
 * @package Model_Mapper_RecurringBillingTransactions
 * 
 * A snapshot of recurring billing transactions
 * 
 */

class Model_Mapper_RecurringBillingTransactions extends Pet_Model_Mapper_Abstract {
    
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
    public function insertTransaction($status, array $data) {
        $mongo = Pet_Mongo::getInstance();
        $data = array_merge(array(
            'timestamp'        => time(),
            'date_r'           => date('Y-m-d H:i:s'),
            'status'           => $status,
        ), $data);
        $mongo->recurring_billing_transactions->insert(
            $data, array('fsync' => false));
    }

    /**
     * @param bool $status
     * @param array $data
     * @param array $gateway_calls
     * @param array $exceptions
     * @return void
     * 
     */
    public function insertErrors($status, array $data) {
        $mongo = Pet_Mongo::getInstance();
        $data = array_merge(array(
            'timestamp'        => time(),
            'date_r'           => date('Y-m-d H:i:s'),
            'status'           => $status,
        ), $data);
        $mongo->recurring_billing_errors->insert(
            $data, array('fsync' => false));
    }
}
