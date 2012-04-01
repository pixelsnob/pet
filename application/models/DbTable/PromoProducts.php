<?php
/**
 * @package Model_DbTable_PromoProducts
 * 
 */
class Model_DbTable_PromoProducts extends Zend_Db_Table_Abstract {

    protected $_name = 'promo_products';

    public function getByPromoId($promo_id) {
        $sel = $this->select()
            ->where('promo_id = ?', $promo_id);
        return $this->fetchAll($sel);
    }

}

