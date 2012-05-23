<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments extends Pet_Model_Mapper_Abstract {

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_model = new Model_OrderPayment($data);
        $order_payments = new Model_DbTable_OrderPayments;
        return $order_payments->insert($op_model->toArray());
    }
}

