<?php
/**
 * @package Model_PaymentType
 * 
 */
class Model_PaymentType extends Pet_Model_Abstract {

    const PAYFLOW       = 1;
    const PAYPAL        = 2;
    const CHECK         = 3;
    
    public $_data = array(
        'id' => null,
        'name' => null,
        'table_name' => null
    );


}

