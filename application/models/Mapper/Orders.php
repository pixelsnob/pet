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
     * @param array $data
     * @return int user_id
     * 
     */
    function insert(array $data) {
        $order = new Model_Order($data);
        $order->promo_id = $data['promo_id'];
        $order->billing_first_name = $data['first_name'];
        $order->billing_last_name = $data['last_name'];
        $order->date_created = $order->date_updated = date('Y-m-d H:i:s');
        $order->total_cost = $data['total'];
        $order_array = $order->toArray();
        unset($order_array['id']);
        return $this->_orders->insert($order_array);
    }
}
