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
    public function validateProduct(Model_Cart_Product $product) {
        $messenger = Zend_Registry::get('messenger');
        switch ($product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    $msg = 'Multiple subscriptions not allowed'; 
                    $messenger->addMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed together';
                    $messenger->addMessage($msg);
                    return false;
                }
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    $msg = 'Digital and print subscriptions not ' .
                        'allowed together';
                    $messenger->addMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    $msg = 'Multiple digital subscriptions not allowed'; 
                    $messenger->addMessage($msg);
                    return false;
                }
                break;
        }
        return true;
    } 
    
    /**
     * @param Model_Promo $promo
     * @return bool
     * 
     */
    public function validatePromo(Model_Promo $promo, $msg = true) {
        $messenger = Zend_Registry::get('messenger');
        $valid = false;
        foreach ($promo->promo_products as $pp) {
            if (in_array($pp->product_id, $this->_cart->getProductIds())) {
                $valid = true;
            }
        }
        if ($msg && !$valid && $this->_cart->products) {
            $messenger->addMessage('A qualifying product is not in your cart');
        }
        return $valid;
    }
}
