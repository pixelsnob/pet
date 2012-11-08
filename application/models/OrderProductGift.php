<?php
/**
 * @package Model_OrderProductGift
 * 
 */
class Model_OrderProductGift extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_product_id' => null,
        'token' => null,
        'redeem_date' => null,
        'redeemer_order_product_id' => null,

        'product_id' => null,
        'product' => null
    );
    
    /**
     * @param bool $refs
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['product']);
            unset($data['product_id']);
        } else {
            $data['product'] = $data['product']->toArray();
        }
        return $data;
    }
}

