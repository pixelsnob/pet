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
        
        'name' => null,
        'description' => null,

        'zone_id' => null,
        'term' => null,
        'is_renewal' => null,

        'download_format_id' => null,
        'date' => null,
        'path' => null,
        'size' => null,
        'thumb' => null,
        'subscriber_only' => null,

        'qty'  => 1
    );
    
}
