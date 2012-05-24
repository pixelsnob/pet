<?php
/**
 * @package Model_DbTable_OrderedProducts
 */
class Model_DbTable_OrderedProducts_DigitalSubscriptions extends Zend_Db_Table_Abstract {

    protected $_name = 'ordered_product_digital_subscriptions';

    /**
     * @param int $user_id
     * @param bool $for_update
     * @return Zend_DbTable_Row  
     */
    public function getUnexpiredByUserId($user_id, $for_update = false) {
        $sel = $this->select()
            ->where('expiration >= ?', date('Y-m-d'))
            ->where('user_id = ?', $user_id);
        if ($for_update) {
            $sel->forUpdate();
        }
        return $this->fetchRow($sel);
    }
}

