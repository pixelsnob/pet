<?php
/**
 * @package Model_DbTable_Products
 * 
 * 
 */
class Model_DbTable_Products extends Zend_Db_Table_Abstract {

    protected $_name = 'products';

    /**
     * @param int $id
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }

}

