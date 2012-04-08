<?php
/**
 * @package Model_DbTable_SubscriptionZones
 * 
 */
class Model_DbTable_SubscriptionZones extends Zend_Db_Table_Abstract {

    protected $_name = 'subscription_zones';

    public function getByName($name) {
        $sel = $this->select()->where('name = ?', $name);
        return $this->fetchRow($sel);
    }
}

