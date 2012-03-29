<?php
/**
 * @package Model_Cart_Payment
 * 
 */
class Model_Cart_Payment extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'payment_method'      => 'credit_card',
        'cc_num'              => '',
        'cc_exp_month'        => '',
        'cc_exp_year'         => '',
        'cc_cvv'              => ''
    );
    
}
