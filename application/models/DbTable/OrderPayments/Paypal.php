<?php
/**
 * @package Model_DbTable_OrderPaymentsPaypal
 * 
 */
class Model_DbTable_OrderPayments_Paypal extends Zend_Db_Table_Abstract {

    protected $_name = 'order_payments_paypal';

    /**
     * @param int $id
     * @return Zend_DbTable_Row
     * 
     */
    public function getByOrderPaymentId($order_payment_id) {
        $sel = $this->select()->where('order_payment_id = ?',
            $order_payment_id);
        return $this->fetchRow($sel);
    } 


}

