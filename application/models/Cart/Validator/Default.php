<?php
/**
 * @package Model_Cart_Validator
 * 
 * 
 */
class Model_Cart_Validator_Default extends Model_Cart_Validator_Abstract {
    
    /**
     * @var string
     * 
     */
    protected $_message = '';

    /**
     * @param Model_Cart_Product $product
     * @return bool
     */
    public function validateProduct(Model_Cart_Product $product) {
        if ($product->isGift()) {
            return true;
        }
        switch ($product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                if ($this->_cart->products->hasSubscription()) {
                    $this->_message = 'Multiple subscriptions not allowed'; 
                    return false;
                }
                if ($this->_cart->products->hasDigitalSubscription()) {
                    $this->_message = 'Digital and print subscriptions not ' .
                        'allowed together';
                    return false;
                }
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                if ($this->_cart->products->hasSubscription()) {
                    $this->_message = 'Digital and print subscriptions not ' .
                        'allowed together';
                    return false;
                }
                if ($this->_cart->products->hasDigitalSubscription()) {
                    $this->_message = 'Multiple digital subscriptions not allowed'; 
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
    public function validatePromo(Model_Promo $promo) {
        $valid = false;
        foreach ($promo->promo_products as $pp) {
            if (in_array($pp->product_id, $this->_cart->products->getIds())) {
                $valid = true;
            }
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
        /*$users_svc = new Service_Users;
        if (!$this->_cart->products->hasRenewal()) {
            return true;
        }
        if (!$users_svc->isAuthenticated()) {
            $this->_message = 'You must be logged in to purchase renewals';
            $this->_cart->removeRenewals();
            return false;
        }
        $old_expiration = null;
        if ($this->_cart->products->hasSubscription()) {
            $ops_mapper = new Model_Mapper_OrderedProducts_Subscriptions; 
            $sub = $ops_mapper->getUnexpiredByUserId(
                $users_svc->getId(), true);

        } elseif ($this->_cart->products->hasDigitalSubscription()) {
            $opds_mapper = new Model_Mapper_OrderedProducts_DigitalSubscriptions; 
            $sub = $opds_mapper->getUnexpiredByUserId(
                $users_svc->getId(), true);
        }
        if (!$sub || !isset($sub->expiration)) {
            $this->_message = 'There was a problem retrieving your existing ' .
                'subscription'; 
            return false;
        }*/
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
    
    /**
     * @return string 
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
}
