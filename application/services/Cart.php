<?php
/**
 * Users service layer
 *
 * @package Service_Cart
 * 
 */
class Service_Cart {
    
    /**
     * @param string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_cart = new Model_Mapper_Cart;
        $this->_cart->setValidator('Model_Cart_Validator_Default');
        $this->_products_svc = new Service_Products;
        $this->_messenger = Zend_Registry::get('messenger');
    }
    
    /**
     * @return Model_Cart
     * 
     */
    public function get() {
        return $this->_cart->get();
    }

    public function update(array $data) {
        $this->_cart->update($data);
    }

    /**
     * @param int $product_id
     * @return bool
     * 
     */
    public function addProduct($product_id) {
        $product = $this->_products_svc->getById($product_id);
        if ($product) {
            if (!$this->_cart->addProduct($product)) {
                return false;
            }
        } else {
            $this->_messenger->addMessage('Product not found');
            return false;
        }
        return true;
    }
    
    /**
     * @param int $product_id
     * @param int $qty
     * @return void
     * 
     */
    public function setProductQty($product_id, $qty) {
        $this->_cart->setProductQty($product_id, $qty);
    }

    /**
     * @param int $product_id
     * @return void
     */
    public function removeProduct($product_id) {
        $this->_cart->removeProduct($product_id);
    }

    /**
     * @return void
     */
    public function reset() {
        $this->_cart->reset();
    }

    /**
     * @param string $code
     * @return void
     * 
     */
    public function addPromo($code) {
        $promo_svc = new Service_Promos;
        $promo = $promo_svc->getUnexpiredPromoByCode($code);
        if ($promo) {
            if (!$this->_cart->addPromo($promo)) {
                return false;
            }
        } else {
            $this->_messenger->addMessage("Promo \"$code\" is not valid");
            return false;
        }
        return true;
    }

    public function getCartForm() {
        $cart = $this->_cart->get();
        $form = new Default_Form_Cart(array(
            'cart' => $cart
        ));
        $form_data = array();
        foreach ($cart->products as $product) {
            $form_data['qty'][$product->product_id] = $product->qty;
        }
        $form->populate($form_data);
        return $form;
    }
}
