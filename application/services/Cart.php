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
                $this->_message = $this->_cart->getMessage();
                return false;
            }
        } else {
            $this->_message = 'Product not found';
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
        $cart = $this->_cart->get();
        if (!strlen(trim($code))) {
            if ($cart->promo) {
                $this->_message = 'Promo removed';
                $this->_cart->removePromo();
            }
            return true;
        }
        $promo_svc = new Service_Promos;
        $promo = $promo_svc->getUnexpiredPromoByCode($code);
        if ($promo && $this->_cart->addPromo($promo)) {
            $this->_message = "Promo $code added";
            return true;
        } else {
            $this->_message = 'Promo is not valid';
            return false;
        }
    }
    
    /**
     * @return void
     * 
     */
    public function removePromo() {
        $this->_cart->removePromo();
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
        // We don't need pw/username fields if user is already logged in
        if ($users_svc->isAuthenticated()) {
            $form->user->removeElement('username');
            $form->user->removeElement('password');
            $form->user->removeElement('confirm_password');
        }
        $form_data = array_merge(
            $cart->billing->toArray(),
            $cart->shipping->toArray(),
            $cart->payment->toArray(),
            array('use_shipping' => $cart->use_shipping)
        );
        // If user is logged in, use that data to populate form, otherwise
        // show saved data if any
        if ($users_svc->isAuthenticated()) {
            $form_data = array_merge(
                $form_data,
                $users_svc->getUser()->toArray(),
                $users_svc->getProfile()->toArray()
            );
        } else {
            $form_data = array_merge(
                $form_data,
                $cart->user->toArray(),
                $cart->user_info->toArray()
            );
        }
        if ($cart->promo) {
            $form_data = array_merge($form_data, array('promo_code' =>
                $cart->promo->code));
        }
        $form->populate($form_data);
        return $form;
    }
    
    /**
     * @param Form_Checkout $form
     * @param array $data
     * @return void
     * 
     */
    public function saveCheckoutForm(Form_Checkout $form, array $data) {
        $cart = $this->_cart->get();
        $this->_cart->setBilling($form->billing->getValues(true));
        $this->_cart->setShipping($form->getShippingValues());
        $this->_cart->setUser($form->user->getValues(true));
        $this->_cart->setUserInfo($form->info->getValues(true));
        $this->_cart->setPayment($form->payment->getValues(true));
        $promo_code = $form->promo->promo_code;
        $existing_promo_code = ($cart->promo ? $cart->promo->code : '');
        if ($promo_code && $promo_code != $existing_promo_code) {
            $promos_mapper = new Model_Mapper_Promos;
            $promo = $promos_mapper->getUnexpiredPromoByCode($promo_code);
            if ($promo) {
                $this->_cart->addPromo($promo);
            }
        } elseif (!strlen(trim($promo_code)) && $existing_promo_code) {
            $this->_cart->removePromo();
        }
        $use_shipping = (isset($data['use_shipping']) ?
            (int) $data['use_shipping'] : 0);
        $this->_cart->setUseShipping($use_shipping);
    }
    
    /**
     * @param array $post
     * @return bool
     * 
     */
    public function process($form) {
        $cart = $this->get();
        $gateway = new Model_Mapper_PaymentGateway;
        $totals = $cart->getTotals();
        $data = array_merge(
            $form->billing->getValues(true),
            $form->payment->getValues(true),
            array('total' => $totals['total']),
            $form->user->getValues(true),
            $form->getShippingValues()
        );
        try {
            if ($cart->hasDigitalSubscription()) {
                exit('not yet');        
            } else {
                return $gateway->processSale($data);
            }
        } catch (Exception $e) {
            // log, email, all that
            //print_r($e); exit;
            return false;
        }

        return false;
        /*$data = array_merge(
            $form->billing->getValues(true),
            $form->payment->getValues(true),
            array('total' => $totals['total']),
            $form->user->getValues(true),
            $form->getShippingValues()
        );
        $transactions = array();
        foreach ($cart->products as $product) {
            switch ($product->product_type_id) {
                //case Model_ProductType::SUBSCRIPTION:
                    
                //    break;
                case Model_ProductType::DIGITAL_SUBSCRIPTION:
                     
                    break;
                default:
                    
                    break;
                //case Model_ProductType::PHYSICAL:
                    
                //    break;
            }
        }

        //$gateway->processAuth($data);
        //print_r($data);
        //print_r($cart);
        exit;*/

        $this->_cart->setConfirmation($this->_cart->get());
        $config = Zend_Registry::get('app_config');
        if ($config['reset_cart_after_process']) {
            $this->_cart->reset();
        }
        return true;
    }

    /**
     * return Model_Confirmation|void
     * 
     */
    public function getConfirmation() {
        return $this->_cart->getConfirmation();
    }

    /**
     * @return string
     * 
     */
    public function getMessage() {
        return $this->_message;
    }
    
}
