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
     * @param int $product_id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getDownloadByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pd' => 'products_downloads'), 'pd.product_id = p.id')
            ->join(array('d' => 'downloads'), 'd.id = pd.download_id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getPhysicalProductByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pp' => 'physical_products'), 'pp.product_id = p.id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getSubscriptionByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('ps' => 'products_subscriptions'),
                'ps.product_id = p.id')
            ->join(array('s' => 'subscriptions'), 's.id = ps.subscription_id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getCourseByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'),
                array('p.*', 'p.id as product_id'))
            ->join(array('pc' => 'products_courses'), 'pc.product_id = p.id')
            ->join(array('c' => 'courses'), 'c.id = pc.course_id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    /**
     * @param int $product_id
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getDigitalSubscriptionByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pds' => 'products_digital_subscriptions'),
                'pds.product_id = p.id')
            ->join(array('ds' => 'digital_subscriptions'),
                'pds.digital_subscription_id = ds.id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    /**
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getSubscriptions() {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('ps' => 'products_subscriptions'),
                'ps.product_id = p.id')
            ->join(array('s' => 'subscriptions'),
                'ps.subscription_id = s.id')
            ->where('p.active')
            ->order(array('s.zone_id', 's.name'));
        return $this->fetchAll($sel);
    }

    /**
     * @param int $zone_id
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return Zend_Db_Table_Row object 
     * 
     */
    public function getSubscriptionsByZoneId($zone_id, $is_giftable = null, $is_renewal = false) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('ps' => 'products_subscriptions'),
                'ps.product_id = p.id')
            ->join(array('s' => 'subscriptions'), 's.id = ps.subscription_id')
            ->where('s.zone_id = ?', $zone_id)
            ->where('p.active')
            ->order('s.name')
            ->where('s.is_renewal = ?', (int) $is_renewal);
        if ($is_giftable !== null) {
            $sel->where('p.is_giftable = ?', (int) $is_giftable);
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
            ->join(array('pds' => 'products_digital_subscriptions'),
                'pds.product_id = p.id')
            ->join(array('ds' => 'digital_subscriptions'),
                'pds.digital_subscription_id = ds.id')
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


}

