<?php
/**
 * @package Model_OrderProduct
 * 
 */
class Model_OrderProduct extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'order_id' => null,
        'product_id' => null,
        'qty' => 0,
        'cost' => 0,
        'discount' => 0,
        'is_gift' => 0,

        'product' => null
    );

    /**
     * Enable direct access to product properties:
     * $product->name instead of $product->product->name 
     * 
     * @param string $field Name of field to get
     * @return mixed Field value
     * 
     */
    public function __get($field) {
        if (!isset($this->_data[$field]) && isset($this->_data['product']->$field)) {
            return $this->_data['product']->$field;
        } else {
            return parent::__get($field);
        }
    }

    /**
     * @return bool
     * 
     */
    public function isSubscription() {
        if ($this->_data['product']->product_type_id == Model_ProductType::SUBSCRIPTION) {
            return true;
        }
        return false;
    }
    
    /**
     * @return bool
     * 
     */
    public function isDigital() {
        return $this->_data['product']->product_type_id ==
            Model_ProductType::DIGITAL_SUBSCRIPTION;
    }

    /**
     * @return bool
     * 
     */
    public function isPhysical() {
        return $this->_data['product']->product_type_id ==
            Model_ProductType::PHYSICAL;
    }

    /**
     * @return bool
     * 
     */
    public function isDownload() {
        return $this->_data['product']->product_type_id ==
            Model_ProductType::DOWNLOAD;
    }

    /**
     * @return bool
     * 
     */
    public function isGift() {
        return $this->_data['is_gift'];
    }

    /** 
     * @return array
     * 
     */
    public function toArray() {
        $data = $this->_data;
        unset($data['product']);
        return $data;
    }
}

