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
     * @param int $user_id
     * @return Zend_DbTable_Row
     * 
     */
    public function getExpirationsByUserId($user_id) {
        $reg_subquery = <<<END
select max(expiration)
from order_product_subscriptions
where user_id = ops_reg.user_id
and digital_only = 0
END;
        $dig_subquery = <<<END
select max(expiration)
from order_product_subscriptions
where user_id = ops_reg.user_id
and digital_only = 1
END;
        $prev_subquery = <<<END
select expiration 
from order_product_subscriptions
where user_id = ops_reg.user_id
and digital_only = 0
order by expiration desc
limit 1, 1
END;
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops_reg' => 'order_product_subscriptions'), array(
                'ops_reg.user_id',
                'ops_reg.expiration as regular',
                'if (ops_reg.expiration < ops_dig.expiration, '.
                    'ops_dig.expiration, ops_reg.expiration) as digital',
                'ops_prev.expiration as previous'
            ))
            ->joinLeft(array('ops_dig' => 'order_product_subscriptions'),
                "ops_reg.user_id = ops_dig.user_id and ops_dig.expiration = " .
                "($dig_subquery)", null)
            ->joinLeft(array('ops_prev' => 'order_product_subscriptions'),
                "ops_reg.user_id = ops_prev.user_id and ops_prev.expiration = " .
                "($prev_subquery)", null)
            ->where("ops_reg.expiration = ($reg_subquery)")
            ->where('ops_reg.user_id = ?', $user_id);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $date
     * @return Zend_DbTable_Rowset
     * 
     */
    public function getByExpiration($date) {
        $db = $this->getAdapter();
        $date = $db->quote($date);
        $min_subquery = 'select min(expiration) from order_product_subscriptions ' .
            'where user_id = ops.user_id ' .
            'and order_product_id = ops.order_product_id';
        $max_subquery = 'select max(expiration) from order_product_subscriptions ' .
            'where ops.user_id = user_id ';
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
            ->where("ops.expiration = $date")
            ->group('ops.user_id');
        return $this->fetchAll($sel);
    }

    /**
     * @param string $region
     * @param string $start_date
     * @return Zend_Db_Table_Rowset
     * 
     */
    public function getMailingListReport($region = null, $start_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $subquery = 'select max(expiration) from order_product_subscriptions ' .
            'where user_id = ops.user_id';     // and expiration > $start_date";
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops' => 'order_product_subscriptions'), array(
                'upper(up.shipping_first_name) as FIRST_NAME_SHIPPING',
                'upper(up.shipping_last_name) as LAST_NAME_SHIPPING',
                'upper(up.shipping_company) as COMPANY_SHIPPING',
                'upper(up.shipping_address) as ADDRESS_SHIPPING',
                'upper(up.shipping_address_2) as ADDRESS2_SHIPPING',
                'upper(up.shipping_city) as CITY_SHIPPING',
                'upper(up.shipping_state) as STATE_SHIPPING',
                'upper(up.shipping_postal_code) as POSTAL_CODE_SHIPPING',
                'upper(up.shipping_country) as COUNTRY_SHIPPING',
                'date_format(ops.expiration, "%m/%Y") as EXPIRATION'
            ))
            ->joinLeft(array('u' => 'users'), 'ops.user_id = u.id', null)
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.user_id', null)
            ->where("ops.expiration = ($subquery)")
            ->where('ops.digital_only = 0')
            ->order('ops.expiration')
            ->where("ops.expiration >= $start_date")
            ->group('ops.user_id');
        if ($region == 'usa') {
            $sel->where("up.shipping_country = 'USA'");
        } elseif ($region == 'intl') {
            $sel->where("up.shipping_country != 'USA'");
        }
        return $this->fetchAll($sel);
    }

    /**
     * @param array $params
     * @return Zend_Db_Table_Rowset
     * 
     */
    public function getSubscribersReport($params) {
        $db = $this->getAdapter();
        $start_date = $db->quote($params['start_date']);
        $end_date = $db->quote($params['end_date']);
        $subquery = 'select max(expiration) from order_product_subscriptions ' .
            "where user_id = ops.user_id";
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('ops' => 'order_product_subscriptions'), array(
                'date_format(ops.expiration, "%m/%d/%Y") as expiration',
                //'ops.expiration',
                'u.email',
                'up.shipping_first_name',
                'up.shipping_last_name',
                'up.shipping_address',
                'up.shipping_address_2',
                'up.shipping_city',
                'up.shipping_state',
                'up.shipping_postal_code',
                'up.version',
                'up.marketing',
                'up.opt_in',
                'up.opt_in_partner'
            ))
            ->joinleft(array('u' => 'users'), 'ops.user_id = u.id', null)
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.user_id', null)
            ->where("ops.expiration = ($subquery)")
            //->having("expiration between $start_date and $end_date")
            ->where("ops.expiration between $start_date and $end_date")
            ->order('ops.expiration')
            ->group('ops.user_id');
        if (isset($params['opt_in']) && $params['opt_in']) {
            $sel->where('up.opt_in = 1');
        }
        if (isset($params['opt_in_partner']) && $params['opt_in_partner']) {
            $sel->where('up.opt_in_partner = 1');
        }
        $sub_type = (isset($params['subscriber_type']) ?
            $params['subscriber_type'] : null);
        if ($sub_type == 'premium') {
            $sel->where('ops.digital_only = 0');
        } elseif ($sub_type == 'digital_only') {
            $sel->where('ops.digital_only = 1');
        }
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

