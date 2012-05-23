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
        'date' => null
    );

}

