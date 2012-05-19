<?php
/**
 * @package Model_OrderedProduct
 * 
 */
class Model_OrderedProduct extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_id' => null,
        'product_id' => null,
        'qty' => 0,
        'total_cost' => 0,
        'discount_cost' => 0
    );

}

