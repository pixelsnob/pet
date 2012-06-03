<?php
/**
 * @package Model_Mapper_OrderPayments
 * 
 */
class Model_Mapper_OrderPayments extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_order_payments = new Model_DbTable_OrderPayments;
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_model = new Model_OrderPayment($data);
        return $this->_order_payments->insert($op_model->toArray());
    }
    
    /**
     * @param int $order_id
     * @return array
     * 
     */
    public function getByOrderId($order_id) {
        $order_payments   = new Model_DbTable_OrderPayments;
        $order_payments   = $this->_order_payments->getByOrderId($order_id);
        $payflow_mapper   = new Model_Mapper_OrderPayments_Payflow;
        $paypal_mapper    = new Model_Mapper_OrderPayments_Paypal;
        $op_array = array();
        if ($order_payments) {
            foreach ($order_payments as $op) {
                $op = new Model_OrderPayment($op->toArray());
                switch ($op->payment_type_id) {
                    case Model_PaymentType::PAYFLOW:
                        $payflow_payment = $payflow_mapper->getByOrderPaymentId(
                            $op->id);
                        if (!$payflow_payment) {
                            $msg = 'order_payment_payflow entry not found';
                            throw new Exception($msg);
                        }
                        $op->pnref = $payflow_payment->pnref;
                        break;
                    case Model_PaymentType::PAYPAL:
                        $paypal_payment = $paypal_mapper->getByOrderPaymentId(
                            $op->id);
                        if (!$paypal_payment) {
                            $msg = 'order_payment_paypal entry not found';
                            throw new Exception($msg);
                        }
                        $op->pnref = $paypal_payment->pnref;
                        break;
                    //case Model_PaymentType::CHECK:
                        
                    //    break;
                }
                $op_array[] = $op;
            }
        }
        return $op_array;
    }
}

