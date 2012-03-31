<?php
/**
 * @package Model_Cart_Validator
 * 
 * 
 */
class Model_Cart_Validator_Default extends Model_Cart_Validator_Abstract {
    
    protected $_message = '';

    /**
     * @param Model_Cart_Product $product
     * @return bool
     */
    public function isProductValid(Model_Cart_Product $product) {
        switch ($product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    $msg = 'Multiple subscriptions not allowed'; 
                    $this->_setMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed in same cart';
                    $this->_setMessage($msg);
                    return false;
                }
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed in same cart';
                    $this->_setMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    $msg = 'Multiple digital subscriptions not allowed'; 
                    $this->_setMessage($msg);
                    return false;
                }
                break;
        }
        return true;
    } 

    protected function _setMessage($message) {
        $this->_message = $message;
    }

    public function getMessage() {
        return $this->_message;
    }
}
