<?php
/**
 * @package Model_DbTable_OrderProductSubscriptions
 * 
 */
class Model_DbTable_OrderProductSubscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'order_product_subscriptions';

    /**
     * @param int $order_id
     * @return Zend_DbTable_Rowset 
     * 
     */
    public function getByOrderId($order_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops' => 'order_product_subscriptions'))
            ->joinLeft(array('op' => 'order_products'),
                'ops.order_product_id = op.id')
            ->where('op.order_id = ?', $order_id);
        return $this->fetchAll($sel);
    }

    /**
     * @param int $user_id
     * @param mixed $digital_only
     * @return Zend_DbTable_Row  
     */
    public function getUnexpiredByUserId($user_id, $digital_only = null) {
        $sel = $this->select()
            ->where('expiration >= ?', date('Y-m-d'))
            ->where('user_id = ?', $user_id)
            ->order(array('expiration desc'));
        if ($digital_only !== null) {
            $sel->where('digital_only = ?', (int) $digital_only);
        }
        return $this->fetchRow($sel);
    }
    
    /**
     * @param string $date
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByExpiration($date) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = <<<END
select *, (
    select min(expiration)
    from order_product_subscriptions ops1
    where ops1.user_id = ops2.user_id
    /* We're only looking for min expirations for this same product id,
       used to determine how many times this has been renewed via
       recurring billing */
    and ops1.order_product_id = ops2.order_product_id
) as min_expiration
from order_product_subscriptions ops2
left join order_products op
on ops2.order_product_id = op.id
where expiration = (
    select max(expiration)
    from order_product_subscriptions ops3
    where ops3.user_id = ops2.user_id
)
group by user_id
having expiration = ?
END;
        $sql = $db->quoteInto($sql, $date);
        return $db->query($sql)->fetchAll();
    }

    /**
     * @param string $date
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getMailingListReport($country = null, $start_date, $end_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $end_date = $db->quote($end_date);
        // Make a list of "subscription" product types
        /*$product_types = implode(',', array(Model_ProductType::SUBSCRIPTION,
            Model_ProductType::DIGITAL_SUBSCRIPTION));
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('op' => 'order_payments'), 'date_format(date, "%m-%d-%Y") ' .
                'as date, op.amount as amount')
            ->joinLeft(array('o' => 'orders'), 'op.order_id = o.id')
            ->joinLeft(array('pf' => 'order_payments_payflow'),
                'op.id = pf.order_payment_id')
            ->joinLeft(array('pp' => 'order_payments_paypal'),
                'op.id = pp.order_payment_id',
                'if (op.payment_type_id = 2, pp.correlationid, pf.pnref) as transid')
            ->joinLeft(array('ops' => 'order_products'), 'o.id = ops.order_id')
            ->joinLeft(array('p' => 'products'), 'ops.product_id = p.id',
                'group_concat(sku separator ";") as sku, ' .
                "group_concat(if (p.product_type_id in($product_types), 3900, 3930) " .
                    'separator ";") as gl_code')
            ->where("op.date between $start_date and $end_date")
            ->group('op.id');
        return $this->fetchAll($sel);*/
    }


}

