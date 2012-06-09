<?php
/**
 * @package Model_Mapper_Orders
 * 
 */
class Model_Mapper_Orders extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_orders = new Model_DbTable_Orders;
    }
    
    /**
     * @param int $id
     * @return Model_Order
     * 
     */
    public function get($id) {
        $order = $this->_orders->find($id);
        if ($order) {
            $order_array = $order->toArray();
            if (isset($order_array[0])) {
                $order_model = new Model_Order($order_array[0]);
                return $order_model;
            }
        }
    }

    /**
     * @param bool $email_sent
     * @return array An array of Model_Order objects
     * 
     */
    public function getByEmailSent($email_sent) {
        $orders = $this->_orders->getByEmailSent($email_sent);
        $out = array();
        if ($orders) {
            foreach ($orders as $order) {
                $out[] = new Model_Order($order->toArray());
            }
        }
        return $out;
    }
    
    /** 
     *  
     * 
     */
    public function getPaginatedFilteredList($page = null, $filters = array(),
                                             $sort = array()) {
        $allowed_filters = array('email', 'date_start', 'date_end');
        $sel = $this->_orders->select();
        // Add date where clauses, if any
        if (in_array('date_start', array_keys($filters))) {
            $date_start = new DateTime($filters['date_start']);
            $date_start->setTime(12, 0, 0);
            $sel->where('date_created >= ?', $date_start->format('Y-m-d H:i:s'));
            unset($filters['date_start']);
        }
        if (in_array('date_end', array_keys($filters))) {
            $date_end = new DateTime($filters['date_end']);
            $date_end->setTime(23, 59, 59);
            $sel->where('date_created <= ?', $date_end->format('Y-m-d H:i:s'));
            unset($filters['date_end']);
        }
        // Add remaining filters
        foreach ($filters as $k => $v) {
            if (strlen(trim($v)) && in_array($k, $allowed_filters)) {
                $sel->where("$k = ?", $v);
            }
        }
        $sel->order('id desc');
        //echo $sel->__toString(); exit;
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if ($page) {
            $paginator->setCurrentPageNumber($page);
        }
        $paginator->setItemCountPerPage(50);
        $orders = array();
        foreach ($paginator as $row) {
            $orders[] = new Model_Order($row);
        }
        return array('paginator' => $paginator, 'data' => $orders);
    }

    /**
     * @param bool $email_sent
     * @return int Num rows updated
     * 
     */
    public function updateEmailSent($order_id, $email_sent) {
        return $this->_orders->update(array('email_sent' => (int)
            $email_sent), $order_id);
    }

    /**
     * @param array $data
     * @return int user_id
     * 
     */
    function insert(array $data) {
        $order = new Model_Order($data);
        $order->promo_id = (strlen(trim($data['promo_id'])) ?
            $data['promo_id'] : null);
        $order->billing_first_name = $data['first_name'];
        $order->billing_last_name = $data['last_name'];
        $order->date_created = $order->date_updated = date('Y-m-d H:i:s');
        $order->total_cost = $data['total'];
        $order_array = $order->toArray();
        unset($order_array['id']);
        return $this->_orders->insert($order_array);
    }
}
