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
        'name' => null,
        'short_description' => null,
        'description' => null,
        'cost' => null,
        'image' => null,
        'active' => null,
        'max_qty' => null,
        'is_giftable' => null,

        'item' => null
    );

    /**
     * Enable direct access to item properties
     * 
     * @param string $field Name of field to get
     * @return mixed Field value
     * 
     */
    public function __get($field) {
        if ($field != 'id' && isset($this->_data['item']->$field)) {
            return $this->_data['item']->$field;
        } else {
            return parent::__get($field);
        }
    }

    /** 
     * @param bool Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['item']);
        } else {
            $data['item'] = $data['item']->toArray();
        }
        return $data;
    }
}
