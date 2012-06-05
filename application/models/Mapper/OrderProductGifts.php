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
        $this->_products_mapper = new Model_Mapper_Products;
    }
    
    /**
     * @param string $token
     * @return Model_OrderProductGift
     *
     */
    public function getByToken($token) {
        $gift = $this->_opg->getByToken($token); 
        if ($gift) {
            $gift_model = new Model_OrderProductGift($gift->toArray());
            $gift_model->product = $this->_products_mapper->getById(
                $gift['product_id']);
            return $gift_model;
        }
    }

    /**
     * @param int $order_id
     * @return array
     * 
     */
    public function getByOrderId($order_id) {
        $gifts = $this->_opg->getByOrderId($order_id); 
        $gifts_array = array();
        if ($gifts) {
            foreach ($gifts as $gift) {
                $gifts_array[] = new Model_OrderProductGift($gift->toArray());
            }
        }
        return $gifts_array;
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

