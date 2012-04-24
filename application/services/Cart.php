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
        $this->_products_svc = new Service_Products;
        $this->_messenger = Zend_Registry::get('messenger');
        $this->_messenger->setNamespace('cart');
    }
    
    /**
     * @return Model_Cart
     * 
     */
    public function get() {
        return $this->_cart->get();
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function update(array $data) {
        $this->_cart->update($data);
    }

    /**
     * @param int $product_id
     * @param int $is_gift
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
        if ($promo && $this->_cart->addPromo($promo)) {
            return true;
        } else {
            $this->_messenger->addMessage("Promo \"$code\" is not valid");
            return false;
        }
    }
    
    /**
     * @return Form_Cart
     * 
     */
    public function getCartForm() {
        $cart = $this->_cart->get();
        $form = new Form_Cart(array(
            'cart' => $cart
        ));
        $form_data = array();
        foreach ($cart->products as $product) {
            $form_data['qty'][$product->product_id] = $product->qty;
        }
        $form->populate($form_data);
        return $form;
    }

    /**
     * @return Form_Checkout
     * 
     */
    public function getCheckoutForm() {
        $cart = $this->_cart->get();
        $identity = Zend_Auth::getInstance()->getIdentity();
        $states = new Zend_Config(require APPLICATION_PATH .
            '/configs/states.php');
        $countries = new Zend_Config(require APPLICATION_PATH .
            '/configs/countries.php');
        $form = new Form_Checkout(array(
            'identity'  => $identity,
            'users'     => new Model_Mapper_Users,
            'states'    => $states->toArray(),
            'countries' => $countries->toArray(),
            'cart'      => $cart,
            'promos'    => new Model_Mapper_Promos
        ));
        $users_svc = new Service_Users;
        if ($users_svc->isAuthenticated()) {
            $form->user->removeElement('username');
            $form->user->removeElement('password');
            $form->user->removeElement('confirm_password');
        }
        if (!$cart->isShippingAddressRequired()) {
            $form->removeSubform('shipping');
        }
        $form_data = array_merge(
            $cart->billing->toArray(),
            $cart->shipping->toArray() 
        );
        if ($users_svc->isAuthenticated()) {
            $user = $users_svc->getUser();
            $form_data = array_merge($form_data, $user->toArray()); 
        } else {
            $form_data = array_merge($form_data, $cart->user->toArray());
        }
        if ($cart->promo) {
            $form_data = array_merge($form_data, array('promo_code' =>
                $cart->promo->code));
        }
        $form->populate($form_data);
        return $form;
    }
    
    /**
     * 
     * 
     */
    public function saveCheckoutForm($data) {
        $cart = $this->_cart->get();
        $this->_cart->saveBilling($data);
        if ($cart->isShippingAddressRequired()) {
            $this->_cart->saveShipping($data);
        }
        $users_svc = new Service_Users;
        if (!$users_svc->isAuthenticated()) {
            $data['password_hash'] = $users_svc->generateHash($data['password']);
            $this->_cart->saveUser($data);
        }
    }
}
