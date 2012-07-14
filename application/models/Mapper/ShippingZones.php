<?php
/**
 * @package Model_Mapper_ShippinZones
 * 
 */
class Model_Mapper_ShippingZones extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_shipping_zones = new Model_DbTable_ShippingZones;
    }
    
    /**
     * @return array
     * 
     */
    public function getAll() {
        $zones = $this->_shipping_zones->fetchAll(
            $this->_shipping_zones->select());
        if ($zones) {
            $out = array();
            foreach ($zones as $zone) {
                $out[] = new Model_ShippingZone($zone->toArray());
            }
            return $out;
        }
    }

}

