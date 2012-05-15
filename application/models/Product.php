<?php
/**
 * @package Model_Product
 * 
 */
class Model_Product extends Pet_Model_Abstract {
    
    public $_data = array(
        'id' => null,
        'product_type_id' => null,
        'sku' => null,
        'cost' => null,
        'image' => null,
        'active' => null,
        'max_qty' => null,
        'is_gift' => null,
        'product_billing_type_id' => null
    );

}
