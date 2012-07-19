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
     * @param bool $expired_check
     * @return Zend_DbTable_Row
     * 
     */
    public function getByCode($code, $expired_check = true) {
        $sel = $this->select()->where('code = ?', $code);
        if ($expired_check) {
            $sel->where('expiration >= ?', date('Y-m-d', time()));
        }
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

    /** 
     * @param array $data
     * @param int $id
     * @return int Num rows updated
     * 
     */
    public function update(array $data, $id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return parent::update($data, $where);
    }

}

