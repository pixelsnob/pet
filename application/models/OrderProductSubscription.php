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
        'order_id' => null,

        'product' => null
    );
    
    /**
     * @param bool $refs Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['min_expiration']);
            unset($data['product']);
            unset($data['order_id']);
        } else {
            $data['product'] = $data['product']->toArray();
        }
        return $data;
    }

}

