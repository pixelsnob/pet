<?php
/**
 * @package Model_OrderPayment
 * 
 */
class Model_OrderPayment extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_id' => null,
        'payment_type_id' => null,
        'amount' => null,
        'date' => null,

        'gateway_data' => null
    );

    /** 
     * @param bool Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['gateway_data']);
        } else {
            $data['gateway_data'] = $data['gateway_data']->toArray();
        }
        return $data;
    }

}

