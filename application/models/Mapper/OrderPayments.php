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
        $order_payments = new Model_DbTable_OrderPayments;
        $order_payments = $this->_order_payments->getByOrderId($order_id);
        $op_array = array();
        if ($order_payments) {
            foreach ($order_payments as $op) {
                $op_array[] = new Model_OrderPayment($op->toArray());
            }
        }
        return $op_array;
    }
}

