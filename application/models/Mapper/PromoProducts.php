<?php
/**
 * @package Model_Mapper_PromoProducts
 * 
 */
class Model_Mapper_PromoProducts extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_promo_products = new Model_DbTable_PromoProducts;
    }
    
    /**
     * @param int $promo_id
     * @return array An array of Model_PromoProduct objects
     * 
     */
    public function getByPromoId($promo_id) {
        $out = array();
        $promo_products = $this->_promo_products->getByPromoId(
            $promo_id);
        foreach ($promo_products as $promo_product) {
            $out[] = new Model_PromoProduct($promo_product->toArray());
        }
        return $out;
    }
}

