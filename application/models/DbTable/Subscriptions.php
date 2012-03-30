<?php
/**
 * @package Model_DbTable_Subscriptions
 * 
 */
class Model_DbTable_Subscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'subscriptions';
    
    public function getByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'))
            ->joinLeft(array('ps' => 'products_subscriptions'),
                'ps.product_id = p.id')
            ->joinLeft(array('s' => 'subscriptions'),
                's.id = ps.subscription_id')
            ->where('ps.product_id = ?', $product_id);
        return $this->fetchRow($sel);
    }

}

