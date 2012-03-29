<?php
/**
 * @package Model_Cart_Products
 * 
 */
class Model_Cart_Products extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'products' => array()
    );
    
    public function add(Model_Cart_Product $product) {
        $this->_data['products'][$product->id] = $product;
    }

    public function remove($product_id) {
        if (in_array($product_id, array_keys($this->_data['products']))) {
            unset($this->_data['products'][$product_id]);
        }
    }
    
    public function setQty($product_id, $qty) {
        if (in_array($product_id, array_keys($this->_data['products']))) {
            $this->_data['products'][$product_id]->setQty($qty);
        }
    }
}
