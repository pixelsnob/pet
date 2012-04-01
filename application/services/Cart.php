<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
class Service_Cart {
    
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_cart = new Model_Mapper_Cart;
        $this->_cart->setValidator(new Model_Cart_Validator_Default);
        $this->_products = new Model_Mapper_Products;
    }
    
    public function get() {
        return $this->_cart->get();
    }

    public function getMessage() {
        return $this->_message;
    }

    public function addProduct($product_id) {
        $product = $this->_products->getById($product_id);
        if ($product) {
            if (!$this->_cart->addProduct($product)) {
                $this->_message = $this->_cart->getMessage();
                return false;
            }
        } else {
            $this->_message = 'Product not found';
            return false;
        }
        return true;
    }
    
    public function setProductQty($product_id, $qty) {
        $this->_cart->setProductQty($product_id, $qty);
    }

    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id);
    }

    public function reset() {
        $this->_cart->reset();
    }

    public function addPromo($code) {
        $promo_svc = new Service_Promos;
        $promo = $promo_svc->getUnexpiredPromoByCode($code);
        if ($promo) {
            if (!$this->_cart->addPromo($promo)) {
                $this->_message = $this->_cart->getMessage();
                return false;
            }
        } else {
            $this->_message = "Promo \"$code\" is not valid";
            return false;
        }
        return true;
    }
}
