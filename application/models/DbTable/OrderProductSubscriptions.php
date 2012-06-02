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
     * @param string $expiration
     * @return Zend_DbTable_Rowset
     * 
     */
    /*public function getByExpiration(DateTime $expiration) {
        $sel = $this->select()
            ->where('expiration = ?', $expiration->format('Y-m-d'));
        return $this->fetchAll($sel);
    }*/

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
    from order_product_subscriptions
    where user_id = ops1.user_id
) as min_expiration, (
    select max(expiration)
    from order_product_subscriptions
    where user_id = ops1.user_id
) as expiration
from order_product_subscriptions ops1
left join order_products op
on op.id = ops1.order_product_id
group by user_id
having expiration = ?
END;
        $sql = $db->quoteInto($sql, $date);
        return $db->query($sql)->fetchAll();
    }

}

