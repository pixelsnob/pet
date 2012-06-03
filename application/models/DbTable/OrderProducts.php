<?php
/**
 * @package Model_DbTable_OrderProducts
 * 
 */
class Model_DbTable_OrderProducts extends Zend_Db_Table_Abstract {

    protected $_name = 'order_products';

    /**
     * @param int $order_id
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByOrderId($order_id) {
        $sel = $this->select()->where('order_id = ?', (int) $order_id);
        return $this->fetchAll($sel);
    }

}

