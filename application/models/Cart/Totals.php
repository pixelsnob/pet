<?php
/**
 * @package Model_Cart_Totals
 * 
 */
class Model_Cart_Totals extends Onone_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'subtotal'             => 0,
        'discount'             => 0,
        // less_ea and less_extra used to calculate discount on individual items
        'discount_less_ea'     => 0,
        'discount_less_extra'  => 0,
        'shipping'             => 0,
        'coupon_total'         => 0,
        'coupon_qty'           => 0,
        'total'                => 0
    );
    
}
