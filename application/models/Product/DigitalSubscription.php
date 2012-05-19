<?php
/**
 * @package Model_Product
 * 
 */
class Model_Product_DigitalSubscription extends Model_Product_Abstract {
    
    public $_data = array(
        'product_id' => null,
        'product_type_id' => null,
        'sku' => null,
        'cost' => null,
        'image' => null,
        'active' => null,
        'name' => null,
        'description' => null,
        'is_renewal' => null,
        'is_gift' => null,
        'is_recurring' => null,
        'term' => null
    );

    /**
     * @return bool
     * 
     */
    public function isGift() {
        return $this->_data['is_gift'];
    }
    
}

