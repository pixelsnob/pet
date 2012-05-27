<?php
/**
 * @package Model_Cart_Product
 * 
 */
class Model_Cart_Product extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'product' => null,
        'qty' => 1,
        'is_gift' => 0
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
        if (isset($this->_data['product']->$field)) {
            return $this->_data['product']->$field;
        } else {
            return parent::__get($field);
        }
    }

    /**
     * @return array
     * 
     */
    public function toArray() {
        return array_merge(
            $this->_data['product']->toArray(),
            array('qty' => $this->_data['qty'])
        );
    }

    /**
     * @return bool
     * 
     */
    public function isSubscription() {
        return $this->_data['product']->product_type_id ==
            Model_ProductType::SUBSCRIPTION;
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
    public function isRenewal() {
        $ptid = $this->_data['product']->product_type_id;
        return (($ptid == Model_ProductType::DIGITAL_SUBSCRIPTION ||
            $ptid == Model_ProductType::SUBSCRIPTION) &&
            $this->_data['product']->is_renewal);
    }
    
    /**
     * Proxy to $product->is_gift
     * 
     * @return bool
     * 
     */
    public function isGift() {
        return $this->_data['product']->is_gift;
    }
}


