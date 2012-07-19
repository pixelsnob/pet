<?php
/**
 * @package Model_Promo
 * 
 */
class Model_Promo extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'code' => null,
        'expiration' => null,
        'description' => null,
        'public_description' => null,
        'receipt_description' => null,
        'banner' => null,
        'discount' => 0,
        'extra_days' => 0,
        'uses' => 0,
        'promo_products' => array()
    );

    /** 
     * @param bool Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['promo_products']);
        }
        return $data;
    }
}

