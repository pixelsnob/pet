<?php
/**
 * @package Model_Mapper_OrderProducts
 * 
 */
class Model_Mapper_OrderProducts extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_order_products = new Model_DbTable_OrderProducts;
    }
    
    /**
     * @param int $order_id
     * @param bool $for_update
     * @return array
     * 
     */
    public function getByOrderId($order_id, $for_update = false) {
        $order_products = $this->_order_products->getByOrderId($order_id,
            $for_update);
        $out = array();
        if ($order_products) {
            foreach ($order_products as $op) {
                $out[] = new Model_OrderProduct($op->toArray());
            }
        }
        return $out;
    }

    /**
     * @param array $data
     * @param int $order_id
     * @return int user_id
     * 
     */
    function insert(array $data, $order_id) {
        $order_product = new Model_OrderProduct($data);
        $order_product->order_id = $order_id;
        $order_product->total_cost = round($data['cost'] * $data['qty'], 2);
        $order_product_array = $order_product->toArray();
        unset($order_product_array['id']);
        return $this->_order_products->insert($order_product_array);
    }
    
}

