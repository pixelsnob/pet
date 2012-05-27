<?php
/**
 * @package Model_Product
 * 
 */
class Model_Product_Subscription extends Model_Product_Abstract {
    
    public $_data = array(
        'product_id' => null,
        'product_type_id' => null,
        'sku' => null,
        'cost' => null,
        'image' => null,
        'active' => null,
        'zone_id' => null,
        'zone' => null,
        'name' => null,
        'description' => null,
        'term' => null,
        'is_renewal' => null
        //'is_gift' => null
    );
    
    /**
     * @return bool
     * 
     */
    public function isGift() {
        return $this->_data['is_gift'];
    }
}

