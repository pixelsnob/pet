<?php

class Model_DbTable_Users extends Zend_Db_Table_Abstract {
    
    /**
     * @var string 
     * 
     */
    protected $_name = 'users';
    
    /**
     * @param int $id
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }
    
    /**
     * @param string $username 
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getByUsername($username) {
        $sel = $this->select()->where('username = ?', $username);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $username 
     * @param bool $is_superuser
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getActiveByUsername($username, $is_superuser = false) {
        $is_superuser = (int) $is_superuser;
        $sel = $this->select()->where('username = ?', $username)
            ->where('is_active = 1');
        if ($is_superuser) {
            $is_superuser = (int) $is_superuser;
            $sel->where('is_superuser = 1');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param string $email
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getActiveByEmail($email) {
        $sel = $this->select()->where('email = ?', $email)
            ->where('is_active = 1');
        return $this->fetchRow($sel);
    }


    /**
     * @param string $email
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getByEmail($email) {
        $sel = $this->select()->where('email = ?', $email);
        return $this->fetchRow($sel);
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

    /**
     * @param string $region
     * @param string $start_date
     * @return Zend_Db_Table_Rowset
     * 
     */
    public function getMailingListReport($region = null, $start_date) {
        $db = $this->getAdapter();
        $start_date = $db->quote($start_date);
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('u' => 'users'), array(
                'upper(up.shipping_first_name) as FIRST_NAME_SHIPPING',
                'upper(up.shipping_last_name) as LAST_NAME_SHIPPING',
                'upper(up.shipping_company) as COMPANY_SHIPPING',
                'upper(up.shipping_address) as ADDRESS_SHIPPING',
                'upper(up.shipping_address_2) as ADDRESS2_SHIPPING',
                'upper(up.shipping_city) as CITY_SHIPPING',
                'upper(up.shipping_state) as STATE_SHIPPING',
                'upper(up.shipping_postal_code) as POSTAL_CODE_SHIPPING',
                'upper(up.shipping_country) as COUNTRY_SHIPPING',
                'date_format(u.expiration, "%m/%Y") as EXPIRATION'
            ))
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.user_id', null)
            ->where('u.expiration >= ?', $start_date)
            ->order('u.expiration')
            ->group('u.id');
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
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('u' => 'users'), array(
                'date_format(u.expiration, "%m/%d/%Y") as expiration',
                'u.email',
                'up.shipping_first_name',
                'up.shipping_last_name',
                'up.shipping_address',
                'up.shipping_address_2',
                'up.shipping_city',
                'up.shipping_state',
                'up.shipping_postal_code',
                'up.shipping_country',
                'up.version',
                'up.marketing',
                'up.opt_in',
                'up.opt_in_partner'
            ))
            ->joinLeft(array('up' => 'user_profiles'), 'u.id = up.user_id', null)
            ->where("u.expiration between $start_date and $end_date")
            ->order('u.expiration');
        if (isset($params['opt_in']) && $params['opt_in']) {
            $sel->where('up.opt_in = 1');
        }
        if (isset($params['opt_in_partner']) && $params['opt_in_partner']) {
            $sel->where('up.opt_in_partner = 1');
        }
        return $this->fetchAll($sel);
    }
}
