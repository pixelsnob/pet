<?php
/**
 * @package Model_DbTable_OrderProductGifts
 * 
 */
class Model_DbTable_OrderProductGifts extends Zend_Db_Table_Abstract {

    protected $_name = 'order_product_gifts';

    /**
     * @param int $order_id
     * @return Zend_DbTable_Row
     * 
     */
    public function getByToken($token) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('opg' => 'order_product_gifts'))
            ->joinLeft(array('op' => 'order_products'),
                'opg.order_product_id = op.id')
            ->where('opg.token = ?', $token);
        return $this->fetchRow($sel);
    }

    /**
     * @param int $order_id
     * @return Zend_DbTable_Rowset 
     * 
     */
    public function getByOrderId($order_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('opg' => 'order_product_gifts'))
            ->joinLeft(array('op' => 'order_products'),
                'opg.order_product_id = op.id')
            ->where('op.order_id = ?', $order_id);
        return $this->fetchAll($sel);
    }

}

