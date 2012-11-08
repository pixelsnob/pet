<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments_Check extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_opc = new Model_DbTable_OrderPayments_Check;
    }

    /**
     * @param int $id
     * @return Model_OrderPayment_Payflow
     * 
     */
    public function getByOrderPaymentId($order_payment_id) {
        $payment = $this->_opc->getByOrderPaymentId($order_payment_id);
        if ($payment) {
            return new Model_OrderPayment_Check($payment->toArray());
        }
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $check_model = new Model_OrderPayment_Check($data);
        return $this->_opc->insert($check_model->toArray());
    }
}
