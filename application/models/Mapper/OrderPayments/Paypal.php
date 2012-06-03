<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments_Paypal extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_opp = new Model_DbTable_OrderPayments_Paypal;
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_paypal_model = new Model_OrderPayment_Paypal($data);
        $order_payments = new Model_DbTable_OrderPayments_Paypal;
        return $order_payments->insert($op_paypal_model->toArray());
    }

    /**
     * @param int $id
     * @return Model_OrderPayment_Paypal
     * 
     */
    public function getByOrderPaymentId($order_payment_id) {
        $payment = $this->_opp->getByOrderPaymentId($order_payment_id);
        if ($payment) {
            return new Model_OrderPayment_Paypal($payment->toArray());
        }
    }
}
