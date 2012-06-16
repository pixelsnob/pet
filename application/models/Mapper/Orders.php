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
     * @param bool $user_id
     * @return array An array of Model_Order objects
     * 
     */
    public function getByUserId($user_id) {
        $orders = $this->_orders->getByUserId($user_id);
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $sel = $this->_orders->select();
        $this->addDateRangeToSelect($sel, 'date_created', $params);
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
        $this->addSortToSelect($sel, 'id', 'desc', $params);
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
     * @param string $start_date
     * @param string $end_date
     * 
     */
    public function getSalesReport($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $start_date->setTime(0, 0, 0);
        $end_date = new DateTime($end_date);
        $end_date->setTime(23, 59, 59);
        $orders = $this->_orders->getSalesReport(
            $start_date->format('Y-m-d H:i:s'),
            $end_date->format('Y-m-d H:i:s')
        );
        $orders_array = array();
        foreach ($orders as $order) {
            $orders_array[] = new Model_Report_Sale($order->toArray());
        }
        return $orders_array;
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
