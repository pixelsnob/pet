<?php
/**
 * @package Model_Product
 * 
 */
class Model_Product extends Pet_Model_Abstract {
    
    const PRODUCT_TYPE_DOWNLOAD     = 1;
    const PRODUCT_TYPE_PHYSICAL     = 2;
    const PRODUCT_TYPE_COURSE       = 3;
    const PRODUCT_TYPE_SUBSCRIPTION = 4;

    public $_data = array(
        'id' => null,
        'product_type_id' => null,
        'sku' => null,
        'cost' => null,
        'image' => null,
        'active' => null
    );

}