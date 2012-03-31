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
     * @return Zend_Db_Table_Row Object 
     * 
     */
    public function getById($id) {
        $sel = $this->select()->where('id = ?', $id);
        return $this->fetchRow($sel);
    }

    public function getDownloadByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pd' => 'products_downloads'), 'pd.product_id = p.id')
            ->join(array('d' => 'downloads'), 'd.id = pd.download_id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }

    public function getPhysicalProductByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'), array('p.*', 'p.id as product_id'))
            ->join(array('pp' => 'physical_products'), 'pp.product_id = p.id')
            ->where('p.id = ?', $product_id)
            ->where('p.active');
        return $this->fetchRow($sel);
    }


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
}

