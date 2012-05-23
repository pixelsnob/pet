<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments_Payflow extends Pet_Model_Mapper_Abstract {
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_payflow_model = new Model_OrderPayment_Payflow($data);
        $order_payments = new Model_DbTable_OrderPayments_Payflow;
        $order_payments->insert($op_payflow_model->toArray());
    }
}
