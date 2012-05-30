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
        'cost' => 0,
        'discount' => 0
    );

}

