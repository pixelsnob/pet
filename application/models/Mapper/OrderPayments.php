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
        $this->_check_mapper = new Model_Mapper_OrderPayments_Check;
    }
 
    /**
     * @param int $id
     * @return Model_OrderPayment
     * 
     */
    public function get($id) {
        $payment = $this->_order_payments->find($id);
        if ($payment) {
            $payment_array = $payment->toArray();
            if (isset($payment_array[0])) {
                $op = new Model_OrderPayment($payment_array[0]);
                switch ($op->payment_type_id) {
                    case Model_PaymentType::PAYFLOW:
                        $op->gateway_data = $this->_payflow_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                    case Model_PaymentType::PAYPAL:
                        $op->gateway_data = $this->_paypal_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                    case Model_PaymentType::CHECK:
                        $op->gateway_data = $this->_check_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                }
                return $op;
            }
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
                        $op->gateway_data = $this->_payflow_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                    case Model_PaymentType::PAYPAL:
                        $op->gateway_data = $this->_paypal_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                    case Model_PaymentType::CHECK:
                        $op->gateway_data = $this->_check_mapper
                            ->getByOrderPaymentId($op->id);
                        break;
                }
                $op_array[] = $op;
            }
        }
        return $op_array;
    }

    /**
     * @param Form_Admin_Report_Transactions $form
     * @return Zend_Db_Table_Rowset 
     */
    public function getTransactionsReport(Form_Admin_Report_Transactions $form) {
        $start_date = new DateTime($form->date_range->start_date->getValue());
        $start_date->setTime(0, 0, 0);
        $end_date = new DateTime($form->date_range->end_date->getValue());
        $end_date->setTime(23, 59, 59);
        $order_payments = $this->_order_payments->getTransactionsReport(
            $start_date->format('Y-m-d H:i:s'),
            $end_date->format('Y-m-d H:i:s')
        );
        return $order_payments;
    }
    
    /** 
     * Builds a query out of search params and paginates the results
     * 
     * @param array $params
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $sel = $this->_order_payments->select()->setIntegrityCheck(false)
            ->from(array('op' => 'order_payments'), array(
                'op.*',
                'op.id',
                'op.order_id',
                'o.email',
                'o.billing_first_name',
                'o.billing_last_name'
            ))
            ->joinLeft(array('o' => 'orders'), 'o.id = op.order_id', null);
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->addDateRangeToSelect($sel, 'op.date', $params);
        if (isset($params['search']) && $params['search']) {
            // If it's a number, try the order id, otherwise, try other text
            // fields
            if (is_numeric($params['search'])) {
                $sel->where('o.id = ?', $params['search']);
            } else {
                // Split search term by whitespace
                $search_parts = explode(' ', $params['search']);
                foreach ($search_parts as $v) {
                    $search = $db->quote('%' . $v . '%');
                    $where = "o.email like $search or o.billing_first_name like $search " .
                        "or o.billing_last_name like $search";
                    $sel->where($where);

                }
            }
        }
        $this->addSortToSelect($sel, 'order_id', 'desc', $params);
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
        $payments = array();
        //echo $sel->__toString(); exit;
        foreach ($paginator as $row) {
            $op = new Model_OrderPayment($row);
            $op->order = new Model_Order($row);
            $payments[] = $op;
        }
        return array('paginator' => $paginator, 'data' => $payments);
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
                $paypal_model = new Model_OrderPayment_Paypal($data);
                $paypal_model->order_payment_id = $opid;
                $this->_paypal_mapper->insert($paypal_model->toArray());
                break;
            case Model_PaymentType::CHECK:
                $check_model = new Model_OrderPayment_Check($data);
                $check_model->order_payment_id = $opid;
                $this->_check_mapper->insert($check_model->toArray());
                break;
        }
    }
}

