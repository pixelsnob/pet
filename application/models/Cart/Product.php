<?php
/**
 * @package Model_Cart_Product
 * 
 */
class Model_Cart_Product extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'id' => null,
        'product_type_id' => null,
        'sku' => null,
        'cost' => null,
        'image' => null,
        'active' => null,
        
        'zone_id' => null,
        'name' => null,
        'description' => null,
        'term' => null,
        'is_renewal' => null,

        'qty'  => 1
    );
    
}
