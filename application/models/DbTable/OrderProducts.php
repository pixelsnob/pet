<?php
/**
 * @package Model_DbTable_OrderProducts
 * 
 */
class Model_DbTable_OrderProducts extends Zend_Db_Table_Abstract {

    protected $_name = 'order_products';

    /**
     * @param int $order_id
     * @param bool $for_update
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByOrderId($order_id, $for_update = false) {
        $sel = $this->select()->where('order_id = ?', (int) $order_id);
        if ($for_update) {
            $sel->forUpdate();
        }
        return $this->fetchAll($sel);
    }

}

