<?php
/**
 * @package Model_DbTable_OrderPayments
 * 
 */
class Model_DbTable_OrderPayments extends Zend_Db_Table_Abstract {

    protected $_name = 'order_payments';

    /**
     * @param int $order_id
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByOrderId($order_id) {
        $sel = $this->select()->where('order_id = ?', $order_id)
                   ->order('date asc');
        return $this->fetchAll($sel);
    }

}

