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

        'gateway_data' => null,
        'order' => null
    );

    /**
     * Enable direct access to gateway_data properties:
     * $payment->pnref instead of $payment->gateway_data->pnref
     * 
     * @param string $field Name of field to get
     * @return mixed Field value
     * 
     */
    public function __get($field) {
        if ($field != 'id' && isset($this->_data['gateway_data']->$field)) {
            return $this->_data['gateway_data']->$field;
        } else {
            return parent::__get($field);
        }
    }

    /** 
     * @param bool Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['gateway_data']);
            unset($data['order']);
        } else {
            $data['gateway_data'] = $data['gateway_data']->toArray();
            if ($data['order']) {
                $data['order'] = $data['order']->toArray();
            }
        }
        return $data;
    }

}

