<?php
/**
 * @package Model_DbTable_Orders
 * 
 */
class Model_DbTable_Orders extends Zend_Db_Table_Abstract {

    protected $_name = 'orders';
    
    /**
     * @param bool $email_sent
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByEmailSent($email_sent) {
        $sel = $this->select()->where('email_sent = ?', (int) $email_sent);
        return $this->fetchAll($sel);
    }

    /**
     * @param bool $user_id
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByUserId($user_id) {
        $sel = $this->select()->where('user_id = ?', $user_id);
        return $this->fetchAll($sel);
    }

    /**
     * @param string $start_date
     * @param string $end_date
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getSalesReport($start_date, $end_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $end_date = $db->quote($end_date);
        /*$subquery = '(select expiration from order_product_subscriptions ' .
            'where user_id = o.user_id ' .
            'order by expiration desc limit 1,1) as previous_expiration';*/
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('o' => 'orders'), array(
                'o.id as order_id',
                'date_format(o.date_created, "%m-%d-%Y") as date',
                'pro.code as promo',
                'group_concat(p.sku) as sku',
                'o.total',
                'o.email',
                'o.billing_first_name',
                'o.billing_last_name',
                'o.billing_address',
                'o.billing_address_2',
                'o.billing_city',
                'o.billing_state',
                'o.billing_country',
                'o.billing_postal_code',
                'o.shipping_address',
                'o.shipping_address_2',
                'o.shipping_city',
                'o.shipping_state',
                'o.shipping_country',
                'o.shipping_postal_code',
                'u.previous_expiration',
                'up.version',
                'up.platform',
                'up.marketing',
                'up.occupation'

            ))
            ->joinLeft(array('op' => 'order_products'), 'op.order_id = o.id', null)
            ->joinLeft(array('p' => 'products'), 'op.product_id = p.id', null)
            ->joinLeft(array('u' => 'users'), 'u.id = o.user_id', null)
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.id', null)
            ->joinLeft(array('pro' => 'promos'), 'o.promo_id = pro.id', null)
            ->where("o.date_created between $start_date and $end_date")
            ->group('o.id')
            ->order('o.id desc');
        return $this->fetchAll($sel);
    }

}

