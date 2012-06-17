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
        /*$db = Zend_Db_Table::getDefaultAdapter();
        $sql = <<<END
select *, (
    select min(expiration)
    from order_product_subscriptions ops1
    where ops1.user_id = ops2.user_id
    and ops1.order_product_id = ops2.order_product_id
) as min_expiration
from order_product_subscriptions ops2
left join order_products op
on ops2.order_product_id = op.id
where expiration = (
    select max(expiration)
    from order_product_subscriptions ops3
    where ops3.user_id = ops2.user_id
    and expiration = ?
)
group by user_id
END;
        $sql = $db->quoteInto($sql, $date);
        return $db->query($sql)->fetchAll();*/
        $db = $this->getAdapter();
        $date = $db->quote($date);
        $min_subquery = 'select min(expiration) from order_product_subscriptions ' .
            'where user_id = ops.user_id ' .
            'and order_product_id = ops.order_product_id';
        $max_subquery = 'select max(expiration) from order_product_subscriptions ' .
            'where ops.user_id = user_id ';
            //"and expiration = $date";
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops' => 'order_product_subscriptions'), array(
                'id',
                'user_id',
                'expiration',
                'order_product_id',
                'expiration as max_expiration',
                "($min_subquery) as min_expiration"))
            ->joinLeft(array('op' => 'order_products'), 'ops.order_product_id = op.id', array(
                'op.order_id', 'op.product_id'))
            ->where("ops.expiration = ($max_subquery)")
            ->group('ops.user_id')
            ->having("expiration = $date");
        //echo $sel->__toString(); exit;
        return $this->fetchAll($sel);
    }

    /**
     * @param string $date
     * @return Zend_Db_Table_Rowset
     * 
     */
    public function getMailingListReport($region = null, $start_date, $end_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $subquery = 'select max(expiration) from order_product_subscriptions ' .
            "where user_id = ops.user_id and expiration > $start_date";
            //"where user_id = ops.user_id";
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops' => 'order_product_subscriptions'),
                'date_format(ops.expiration, "%m/%Y") as EXPIRATION')
            ->joinLeft(array('u' => 'users'), 'ops.user_id = u.id', null)
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.user_id', array(
                'up.shipping_first_name as FIRST_NAME_SHIPPING',
                'up.shipping_last_name as LAST_NAME_SHIPPING',
                'up.shipping_company as COMPANY_SHIPPING',
                'up.shipping_address as ADDRESS_SHIPPING',
                'up.shipping_address_2 as ADDRESS2_SHIPPING',
                'up.shipping_city as CITY_SHIPPING',
                'up.shipping_state as STATE_SHIPPING',
                'up.shipping_postal_code as POSTAL_CODE_SHIPPING',
                'up.shipping_country as COUNTRY_SHIPPING'))
            ->where("ops.expiration = ($subquery)")
            ->where('ops.digital_only = 0')
            ->order('ops.expiration')
            ->group('ops.user_id');
        if ($region == 'usa') {
            $sel->where("up.shipping_country = 'USA'");
        } elseif ($region == 'intl') {
            $sel->where("up.shipping_country != 'USA'");
        }
        return $this->fetchAll($sel);
    }


}

