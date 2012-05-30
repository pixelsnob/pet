<?php
/**
 * @package Model_Mapper_OrderedProducts
 * 
 */
class Model_Mapper_OrderedProducts extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_ordered_products = new Model_DbTable_OrderedProducts;
    }
    
    /**
     * @param int $order_id
     * @param bool $for_update
     * @return array
     * 
     */
    public function getByOrderId($order_id, $for_update = false) {
        $ordered_products = $this->_ordered_products->getByOrderId($order_id,
            $for_update);
        $out = array();
        if ($ordered_products) {
            foreach ($ordered_products as $op) {
                $out[] = new Model_OrderedProduct($op->toArray());
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
        $ordered_product = new Model_OrderedProduct($data);
        $ordered_product->order_id = $order_id;
        $ordered_product->total_cost = round($data['cost'] * $data['qty'], 2);
        $ordered_product_array = $ordered_product->toArray();
        unset($ordered_product_array['id']);
        return $this->_ordered_products->insert($ordered_product_array);
    }
    
}

