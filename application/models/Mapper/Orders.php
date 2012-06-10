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
     * Builds a query out of search params and paginates the results
     * 
     * @param array $params
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $sel = $this->_orders->select();
        $db = Zend_Db_Table::getDefaultAdapter();
        // Add date where clauses, if any
        if (isset($params['start_date']) && $params['start_date']) {
            $start_date = new DateTime($params['start_date']);
            $start_date->setTime(12, 0, 0);
            $sel->where('date_created >= ?', $start_date->format('Y-m-d H:i:s'));
        }
        if (isset($params['end_date']) && $params['end_date']) {
            $end_date = new DateTime($params['end_date']);
            $end_date->setTime(23, 59, 59);
            $sel->where('date_created <= ?', $end_date->format('Y-m-d H:i:s'));
        }
        if (isset($params['search']) && $params['search']) {
            // If it's a number, try the order id, otherwise, try other text
            // fields
            if (is_numeric($params['search'])) {
                $sel->where('id = ?', $params['search']);
            } else {
                // Split search term by whitespace
                $search_parts = explode(' ', $params['search']);
                foreach ($search_parts as $v) {
                    $search = $db->quote('%' . $v . '%');
                    $where = "email like $search or billing_first_name like $search " .
                        "or billing_last_name like $search";
                    $sel->where($where);

                }
            }
        }
        $sort = (isset($params['sort']) ? $params['sort'] : 'id');
        $sort_dir = (isset($params['sort_dir']) ? $params['sort_dir'] : 'desc');
        $sel->order($sort . ' ' . $sort_dir);
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
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
