<?php
/**
 * @package Model_DbTable_PhysicalProducts
 * 
 */
class Model_DbTable_PhysicalProducts extends Zend_Db_Table_Abstract {

    protected $_name = 'physical_products';

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

