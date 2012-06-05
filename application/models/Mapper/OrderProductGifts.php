<?php
/**
 * @package Model_Mapper_OrderProductGifts
 * 
 * 
 */
class Model_Mapper_OrderProductGifts extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_opg = new Model_DbTable_OrderProductGifts;
    }
    
    /**
     * @param array $data
     * @return int user_id
     * 
     */
    function insert(array $data) {
        $opg = new Model_OrderProductGift($data);
        $opg_array = $opg->toArray();
        unset($opg_array['id']);
        return $this->_opg->insert($opg_array);
    }
    
}

