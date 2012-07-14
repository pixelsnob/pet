<?php
/**
 * @package Model_DbTable_ShippingZones
 * 
 */
class Model_DbTable_ShippingZones extends Zend_Db_Table_Abstract {

    protected $_name = 'shipping_zones';

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

