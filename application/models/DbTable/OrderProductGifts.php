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
    public function getUnredeemedByToken($token) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('opg' => 'order_product_gifts'))
            ->joinLeft(array('op' => 'order_products'),
                'opg.order_product_id = op.id')
            ->where('opg.token = ?', $token)
            ->where('opg.redeemer_order_product_id is null');
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

