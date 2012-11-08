<?php
/**
 * @package Model_RecurringBilling
 * 
 */
class Model_RecurringBilling extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_id' => null,
        'payment_type_id' => null,
        'start_date' => null,
        'end_date' => null,
        'next_bill_date' => null,
        'next_bill_amount' => null,
        'original_pnref' => null,
        'original_correlation_id' => null,
        'is_active' => 1
    );

}
