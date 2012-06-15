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
        $subquery = '(select expiration from order_product_subscriptions ' .
            'where user_id = o.user_id limit 1,1) as previous_expiration';
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('o' => 'orders'), array('o.*', $subquery))
            ->joinLeft(array('op' => 'order_products'),
                        'op.order_id = o.id')
            ->joinLeft(array('p' => 'products'), 'op.product_id = p.id',
                        'group_concat(p.sku) as sku')
            ->joinLeft(array('up' => 'user_profiles'), 'o.user_id = up.id',
                array('version', 'platform', 'marketing', 'occupation'))
            ->where("o.date_created between $start_date and $end_date")
            ->group('o.id');
        return $this->fetchAll($sel);
    }

}

