<?php
/**
 * Promotions service layer
 *
 * @package Service_Promos
 * 
 */
class Service_Promos extends Pet_Service {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_promos = new Model_Mapper_Promos;
    }
    
    /**
     * @param string $code
     * @return Model_Promo 
     * 
     */
    public function getUnexpiredPromoByCode($code) {
        return $this->_promos->getUnexpiredPromoByCode($code);
    }
}
