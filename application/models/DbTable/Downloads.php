<?php
/**
 * @package Model_DbTable_Downloads
 * 
 */
class Model_DbTable_Downloads extends Zend_Db_Table_Abstract {

    protected $_name = 'downloads';

    public function getByProductId($product_id) {
        $sel = $this->select()->setIntegrityCheck(false)
            ->from(array('p' => 'products'))
            ->joinLeft(array('pd' => 'products_downloads'),
                'pd.product_id = p.id')
            ->joinLeft(array('d' => 'downloads'),
                'd.id = pd.download_id')
            ->where('pd.product_id = ?', $product_id);
        return $this->fetchRow($sel);
    }

}

