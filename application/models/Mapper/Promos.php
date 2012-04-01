<?php
/**
 * @package Model_Mapper_Promos
 * 
 */
class Model_Mapper_Promos extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_promos = new Model_DbTable_Promos;
    }

    /**
     * @param string $code Promo code
     * @return Model_Promo|void
     * 
     */
    public function getUnexpiredPromoByCode($code) {
        $promo_products_mapper = new Model_Mapper_PromoProducts;
        $promo = $this->_promos->getUnexpiredPromoByCode($code);
        if ($promo) {
            $promo = new Model_Promo($promo->toArray());
            $promo->promo_products = $promo_products_mapper->getByPromoId($promo->id);
            return $promo;
        }
    }
}

