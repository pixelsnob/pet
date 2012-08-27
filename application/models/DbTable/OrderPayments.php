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

    /**
     * @param string $start_date
     * @param string $end_date
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getTransactionsReport($start_date, $end_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $end_date = $db->quote($end_date);
        // Make a list of "subscription" product types
        $product_types = implode(',', array(Model_ProductType::SUBSCRIPTION,
            Model_ProductType::DIGITAL_SUBSCRIPTION));
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('op' => 'order_payments'), array(
                '("1000") as CUSTOMER_TYPE',
                'op.order_id as ID',
                'date_format(date, "%m-%d-%Y") as DATE',
                '(0) as UNKNOWN',
                'o.billing_address as ADDRESS',
                'o.billing_address_2 as ADDRESS_2',
                'o.billing_city as CITY',
                'o.billing_state as STATE',
                'o.billing_postal_code as POSTAL_CODE',
                'if (op.payment_type_id = 2, pp.correlationid, pf.pnref) as TRAN_ID',
                'p.sku as CODE',
                'ops.qty as UNIT',
                // This makes sure to take discounts, shipping, and partial credits into account
                'round((ops.cost / (o.total + o.discount - o.shipping)) * op.amount, 2) * ops.qty as AMOUNT_POS_NEG', 
                "if (p.product_type_id in($product_types), 3900, 3930) " .
                    'as GL_CODE'
            ))
            ->joinLeft(array('o' => 'orders'), 'op.order_id = o.id', null)
            ->joinLeft(array('pf' => 'order_payments_payflow'),
                'op.id = pf.order_payment_id', null)
            ->joinLeft(array('pp' => 'order_payments_paypal'),
                'op.id = pp.order_payment_id', null)
            ->joinLeft(array('ops' => 'order_products'), 'o.id = ops.order_id', null)
            ->joinLeft(array('p' => 'products'), 'ops.product_id = p.id', null)
            ->where("op.date between $start_date and $end_date");
        //echo $sel->__toString();
        //exit;
        return $this->fetchAll($sel);
    }
}

