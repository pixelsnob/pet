<?php
/**
 * @package Model_OrderSubscription
 * 
 */
class Model_OrderProductSubscription extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'user_id' => null,
        'order_product_id' => null,
        'expiration' => null,
        'digital_only' => 0,
        'min_expiration' => null,
        'product' => null
    );
    
    /**
     * @return array
     * 
     */
    public function toArray() {
        $data = $this->_data;
        unset($data['min_expiration']);
        unset($data['product']);
        return $data;
    }

}

