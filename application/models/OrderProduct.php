<?php
/**
 * @package Model_OrderProduct
 * 
 */
class Model_OrderProduct extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_id' => null,
        'product_id' => null,
        'qty' => 0,
        'cost' => 0,
        'discount' => 0,

        'product' => null
    );

    /** 
     * @return array
     * 
     */
    public function toArray() {
        $data = $this->_data;
        unset($data['product']);
        return $data;
    }
}

