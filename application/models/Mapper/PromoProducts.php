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
    
    /**
     * @param array $product_ids
     * @param int $promo_id
     * @return void
     * 
     */
    public function updateByPromoId(array $product_ids, $promo_id) {
        $this->deleteByPromoId($promo_id);
        foreach ($product_ids as $product_id) {
            $this->_promo_products->insert(array(
                'promo_id'   => $promo_id,
                'product_id' => $product_id
            ));
        }
    }

}

