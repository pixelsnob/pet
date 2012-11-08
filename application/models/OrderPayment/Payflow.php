<?php
/**
 * @package Model_OrderPayment_Payflow
 * 
 */
class Model_OrderPayment_Payflow extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_payment_id' => null,
        'cc_number' => null,
        'cc_expiration_month' => null,
        'cc_expiration_year' => null,
        'pnref' => null,
        'ppref' => null,
        'correlationid' => null,
        'cvv2match' => null
    );

}

