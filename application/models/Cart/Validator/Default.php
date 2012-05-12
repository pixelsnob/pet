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
        if ($product->is_gift) {
            return true;
        }
        //$messenger = Zend_Registry::get('messenger');
        //$messenger->setNamespace('cart');
        switch ($product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    //$msg = 'Multiple subscriptions not allowed'; 
                    //$messenger->addMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    //$msg = 'Digital and print subscriptions not ' .
                    //    'allowed together';
                    //$messenger->addMessage($msg);
                    return false;
                }
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                if ($this->_cart->hasSubscription()) {
                    //$msg = 'Digital and print subscriptions not ' .
                        'allowed together';
                    //$messenger->addMessage($msg);
                    return false;
                }
                if ($this->_cart->hasDigitalSubscription()) {
                    //$msg = 'Multiple digital subscriptions not allowed'; 
                    //$messenger->addMessage($msg);
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
        //$messenger = Zend_Registry::get('messenger');
        //$messenger->setNamespace('cart');
        $valid = false;
        foreach ($promo->promo_products as $pp) {
            if (in_array($pp->product_id, $this->_cart->getProductIds())) {
                $valid = true;
            }
        }
        if ($msg && !$valid && $this->_cart->products) {
            //$messenger->addMessage('Promo is not valid');
        }
        return $valid;
    }
    
    /**
     * If cart has renewals, and user is not logged in, renewals are removed
     * from cart
     * 
     * @return bool 
     */
    public function validateRenewals() {
        $users_svc = new Service_Users;
        //$messenger = Zend_Registry::get('messenger');
        //$messenger->setNamespace('cart');
        if ($this->_cart->hasRenewal() && !$users_svc->isAuthenticated()) {
            $msg = 'You must be logged in to purchase renewals';
            //$messenger->clearMessages();
            //$messenger->addMessage($msg);
            $this->_cart->removeRenewals();
            return false;
        }
        return true;
    }

    /**
     * For validating the state of the cart at any given time
     * 
     * @return bool
     */
    public function validate() {
        return $this->validateRenewals();
    }
}
