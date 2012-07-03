<?php
/**
 * @package Model_DbTable_Subscriptions
 * 
 */
class Model_DbTable_Subscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'subscriptions';

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

