<?php
/**
 * @package Model_DbTable_Promos
 * 
 */
class Model_DbTable_Promos extends Zend_Db_Table_Abstract {

    protected $_name = 'promos';

    /**
     * @param int $id
     * @return Zend_DbTable_Row
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }
    
    /**
     * @param string $code
     * @return Zend_DbTable_Row
     * 
     */
    public function getUnexpiredPromoByCode($code) {
        $sel = $this->select()->where('code = ?', $code)
            ->where('expiration >= ?', date('Y-m-d', time()));
        return $this->fetchRow($sel);
    }

    /** 
     * @param string $banner_filename
     * @param int $id
     * @return int Num rows updated
     * 
     */
    public function updateBanner($banner_filename, $id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return parent::update(array('banner' => $banner_filename), $where);
    }
}

