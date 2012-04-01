<?php
/**
 * Promotions service layer
 *
 * @package Service_Promos
 * 
 */
class Service_Promos {
    
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_promos = new Model_Mapper_Promos;
    }
    
    public function getUnexpiredPromoByCode($code) {
        return $this->_promos->getUnexpiredPromoByCode($code);
    }
}
