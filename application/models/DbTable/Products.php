<?php
/**
 * @package Model_DbTable_Products
 * 
 * 
 */
class Model_DbTable_Products extends Zend_Db_Table_Abstract {

    protected $_name = 'products';

    /**
     * @param int $id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }

    /**
     * @param string $sku
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getBySku($sku) {
        $sel = $this->select()->where('sku = ?', $sku);
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_type_id
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getByProductType($product_type_id) {
        $sel = $this->select()->where('product_type_id = ?', $product_type_id);
        return $this->fetchAll($sel);
    }

    /**
     * @param int $product_id
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getDownloadByProductId($product_id, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('d' => 'downloads'), 'd.product_id = p.id')
            ->where('p.id = ?', $product_id);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getPhysicalProductByProductId($product_id, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pp' => 'physical_products'), 'pp.product_id = p.id')
            ->where('p.id = ?', $product_id);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getSubscriptionByProductId($product_id, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('s' => 'subscriptions'), 's.product_id = p.id')
            ->where('p.id = ?', $product_id);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param int $term
     * @param int $zone
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getSubscriptionByTermAndZone($term, $zone, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('s' => 'subscriptions'), 's.product_id = p.id')
            ->where('s.term_months = ?', $term)
            ->where('s.zone_id = ?', $zone);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getCourseByProductId($product_id, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'),
                array('p.*', 'p.id as product_id'))
            ->join(array('c' => 'courses'), 'c.product_id = p.id')
            ->where('p.id = ?', $product_id);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @param bool $is_active_check
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getDigitalSubscriptionByProductId($product_id, $is_active_check = true) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('ds' => 'digital_subscriptions'),
                'p.id = ds.product_id')
            ->where('p.id = ?', $product_id);
        if ($is_active_check) {
            $sel->where('p.active');
        }
        return $this->fetchRow($sel);
    }

    /**
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getSubscriptions() {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('s' => 'subscriptions'),
                'p.id = s.product_id')
            ->where('p.active')
            ->order(array('s.zone_id', 'p.name'));
        return $this->fetchAll($sel);
    }

    /**
     * @param int $zone_id
     * @param int $term Term in months 
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getSubscriptionsByZoneIdAndTerm($zone_id,
                                                    $term = null,
                                                    $is_giftable = null,
                                                    $is_renewal = false) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('s' => 'subscriptions'), 's.product_id = p.id')
            ->where('s.zone_id = ?', $zone_id)
            ->where('p.active')
            ->order('p.name')
            ->where('s.is_renewal = ?', (int) $is_renewal);
        if ($is_giftable !== null) {
            $sel->where('p.is_giftable = ?', (int) $is_giftable);
        }
        if ($term !== null) {
            $sel->where('s.term_months = ?', $term);
        }
        return $this->fetchAll($sel);
    }

    /**
     * @param bool $is_gift
     * @param bool $is_renewal
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getDigitalSubscriptions($is_giftable = null, $is_renewal = false) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('ds' => 'digital_subscriptions'),
                'p.id = ds.product_id')
            ->where('p.active')
            ->where('ds.is_renewal = ?', (int) $is_renewal);
        if ($is_giftable !== null) {
            $sel->where('p.is_giftable = ?', (int) $is_giftable);
        }
        return $this->fetchAll($sel);
    }

    /**
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getPhysicalProducts() {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pp' => 'physical_products'),
                'pp.product_id = p.id')
            ->where('p.active')
            ->order('pp.sequence');
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

