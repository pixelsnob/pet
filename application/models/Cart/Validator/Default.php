<?php
/**
 * @package Model_Cart_Validator
 * 
 * 
 */
class Model_Cart_Validator_Default extends Model_Cart_Validator_Abstract {
    
    /**
     * @param Model_Cart_Product $product
     * @return bool
     */
    public function isProductValid(Model_Cart_Product $product) {
        switch ($product->product_type_id) {
            case Model_ProductType::DOWNLOAD:
                
                break;
            case Model_ProductType::PHYSICAL:
                
                break;
            case Model_ProductType::COURSE:
                
                break;
            case Model_ProductType::SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    throw new Exception(
                        'Multiple subscriptions not allowed'); 
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed in same cart';
                    throw new Exception($msg); 
                }
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed in same cart';
                    throw new Exception($msg); 
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    throw new Exception(
                        'Multiple digital subscriptions not allowed'); 
                }
                break;
        }
        return true;
    } 

}
