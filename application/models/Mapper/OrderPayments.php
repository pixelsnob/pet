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
        $this->_payflow_mapper = new Model_Mapper_OrderPayments_Payflow;
        $this->_paypal_mapper = new Model_Mapper_OrderPayments_Paypal;
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $op_model = new Model_OrderPayment($data);
        $opid = $this->_order_payments->insert($op_model->toArray());
        switch ($op_model->payment_type_id) {
            case Model_PaymentType::PAYFLOW:
                $payflow_model = new Model_OrderPayment_Payflow($data);
                $payflow_model->order_payment_id = $opid;
                $this->_payflow_mapper->insert($payflow_model->toArray());
                break;
            case Model_PaymentType::PAYPAL:
                $paypal_model = new Model_OrderPayment_Paypal($data);;
                $paypal_model->order_payment_id = $opid;
                $this->_paypal_mapper->insert($paypal_model->toArray());
                break;
        }
    }
    
    /**
     * @param int $order_id
     * @return array
     * 
     */
    public function getByOrderId($order_id) {
        $order_payments   = $this->_order_payments->getByOrderId($order_id);
        $op_array = array();
        if ($order_payments) {
            foreach ($order_payments as $op) {
                $op = new Model_OrderPayment($op->toArray());
                switch ($op->payment_type_id) {
                    case Model_PaymentType::PAYFLOW:
                        $payflow_payment = $this->_payflow_mapper->getByOrderPaymentId(
                            $op->id);
                        if (!$payflow_payment) {
                            $msg = 'Payflow entry not found for order_payment_id ' .
                                $op->id;
                            throw new Exception($msg);
                        }
                        $op->gateway_data = $payflow_payment;
                        break;
                    case Model_PaymentType::PAYPAL:
                        $paypal_payment = $this->_paypal_mapper->getByOrderPaymentId(
                            $op->id);
                        if (!$paypal_payment) {
                            $msg = 'Paypal entry not found for order_payment_id ' .
                                $op->id;
                            throw new Exception($msg);
                        }
                        $op->gateway_data = $paypal_payment;
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

