<?php
/**
 * @package Model_DbTable_SubscriptionZones
 * 
 */
class Model_DbTable_SubscriptionZones extends Zend_Db_Table_Abstract {
    
    /**
     * @var string
     * 
     */
    protected $_name = 'subscription_zones';

    /**
     * @param string $name
     * @return mixed
     * 
     */
    public function getByName($name) {
        $sel = $this->select()->where('name = ?', $name);
        return $this->fetchRow($sel);
    }
    
    /**
     * @param int $id
     * @return mixed
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }
}

