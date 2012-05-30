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
     * @param bool $email_sent
     * @param bool $for_update
     * @return array An array of Model_Order objects
     * 
     */
    public function getByEmailSent($email_sent, $for_update = false) {
        $orders = $this->_orders->getByEmailSent($email_sent, $for_update);
        $out = array();
        if ($orders) {
            foreach ($orders as $order) {
                $out[] = new Model_Order($order->toArray());
            }
        }
        return $out;
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
