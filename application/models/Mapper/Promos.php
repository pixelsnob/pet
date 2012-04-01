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
     * 
     */
    public function getUnexpiredPromoByCode($code) {
        $promo = $this->_promos->getUnexpiredPromoByCode($code);
        if ($promo) {
            return new Model_Promo($promo->toArray());
        }
    }
}

