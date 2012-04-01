<?php
/**
 * @package Model_DbTable_Promos
 * 
 */
class Model_DbTable_Promos extends Zend_Db_Table_Abstract {

    protected $_name = 'promos';
    
    public function getUnexpiredPromoByCode($code) {
        $sel = $this->select()->where('code = ?', $code)
            ->where('expiration >= ?', date('Y-m-d', time()));
        return $this->fetchRow($sel);
    }
}

