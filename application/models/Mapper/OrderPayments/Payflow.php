<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments_Payflow extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_opp = new Model_DbTable_OrderPayments_Payflow;
    }

    /**
     * @param int $id
     * @return Model_OrderPayment_Payflow
     * 
     */
    public function getByOrderPaymentId($order_payment_id) {
        $payment = $this->_opp->getByOrderPaymentId($order_payment_id);
        if ($payment) {
            return new Model_OrderPayment_Payflow($payment->toArray());
        }
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_payflow_model = new Model_OrderPayment_Payflow($data);
        return $this->_opp->insert($op_payflow_model->toArray());
    }
}
