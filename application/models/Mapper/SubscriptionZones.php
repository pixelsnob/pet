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
    
    /**
     * @param string $name
     * @return Model_SubscriptionZone
     * 
     */
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

    /**
     * @param int $id
     * @return Model_SubscriptionZone
     * 
     */
    public function getById($id) {
        $sz = $this->_subscription_zones->getById($id);
        if ($sz) {
            return new Model_SubscriptionZone($sz->toArray());
        }
    }
}

