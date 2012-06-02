<?php
/**
 * @package Model_DbTable_OrderProductSubscriptions
 * 
 */
class Model_DbTable_OrderProductSubscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'order_product_subscriptions';

    /**
     * @param int $user_id
     * @param mixed $digital_only
     * @param bool $for_update
     * @return Zend_DbTable_Row  
     */
    public function getUnexpiredByUserId($user_id, $digital_only = null,
                                         $for_update = false) {
        $sel = $this->select()
            ->where('expiration >= ?', date('Y-m-d'))
            ->where('user_id = ?', $user_id)
            ->order(array('expiration desc'));
        if ($digital_only !== null) {
            $sel->where('digital_only = ?', (int) $digital_only);
        }
        if ($for_update) {
            $sel->forUpdate();
        }
        return $this->fetchRow($sel);
    }
    
    /**
     * @param string $date
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByExpiration($date, $for_update) {
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
for update
END;
        $sql = $db->quoteInto($sql, $date);
        return $db->query($sql)->fetchAll();
    }

}

