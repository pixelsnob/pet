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
    /*protected $_data = array(
        'product_id' => null,
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
    );*/

    protected $_data = array(
        'product' => null,
        'qty' => 1
    );

    /**
     * Enable direct access to product properties: eliminates need to type
     * $product->name instead of $product->product->name 
     * 
     * @param string $field Name of field to get
     * @return mixed Field value
     * 
     */
    public function __get($field) {
        if (isset($this->_data['product']->$field)) {
            return $this->_data['product']->$field;
        } else {
            return parent::__get($field);
        }
    }
}
