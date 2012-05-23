<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments_Paypal extends Pet_Model_Mapper_Abstract {
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_paypal_model = new Model_OrderPayment_Paypal($data);
        $order_payments = new Model_DbTable_OrderPayments_Paypal;
        $order_payments->insert($op_paypal_model->toArray());
    }
}
