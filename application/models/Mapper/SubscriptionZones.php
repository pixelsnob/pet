<?php
/**
 * @package Model_Mapper_SubscriptionZones
 * 
 * 
 */
class Model_Mapper_SubscriptionZones extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_subscription_zones = new Model_DbTable_SubscriptionZones;
    }
    
    public function getByName($name) {
        $name = strtolower($name);
        if ($name != 'usa' && $name != 'canada') {
            $name = 'international'; 
        }
        $sz = $this->_subscription_zones->getByName($name);
        if ($sz) {
            return new Model_SubscriptionZone($sz->toArray());
        }
    }
}

