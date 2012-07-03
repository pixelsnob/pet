<?php
/**
 * @package Model_DbTable_Downloads
 * 
 */
class Model_DbTable_Downloads extends Zend_Db_Table_Abstract {

    protected $_name = 'downloads';

    /** 
     * @param array $data
     * @param int $product_id
     * @return int Num rows updated
     * 
     */
    public function updateByProductId(array $data, $product_id) {
        $where = $this->getAdapter()->quoteInto('product_id = ?', $product_id);
        return parent::update($data, $where);
    }

}

