<?php

class CartController extends Zend_Controller_Action {

    public function init() {
        $this->_cart_svc = new Service_Cart;
        $this->_cart_mapper = new Model_Mapper_Cart;
        $this->_messenger = $this->_helper->FlashMessenger;
        $this->_messenger->setNamespace('cart');
        $this->view->getHelper('serverUrl')->setScheme('https');
    }

    public function indexAction() {
        $cart = $this->_cart_mapper->get();
        if ($this->_request->isXmlHttpRequest() &&
                !$this->_request->getParam('nolayout')) {
            $json = array(
                'cart'   => $cart->toArray(),
                'totals' => $cart->getTotals()
            );
            $this->_helper->json($json);
            return;
        }
        $this->view->cart_products = $cart->products->getUaSorted(function($a, $b) {
            return ((($a->product_type_id == Model_ProductType::SUBSCRIPTION ||
                $a->product_type_id == Model_ProductType::DIGITAL_SUBSCRIPTION) && !$a->is_gift) ? -1 : 1);
        });
        $this->view->cart = $cart;
        $cart_form = $this->_cart_svc->getCartForm();
        $this->view->cart_form = $cart_form;
        $post = $this->_request->getPost();
        if ($this->_request->isPost()) {
            if ($cart_form->isValid($post)) {
                $this->_cart_mapper->update($post);
                $msg = 'Cart updated';
            } else {
                $msg = 'Submitted information is not valid';
            }
            $this->_messenger->addMessage($msg);
            $this->view->messages = $this->_messenger->getCurrentMessages();
        } else {
            $this->view->messages = $this->_messenger->getMessages();
        }
        $this->view->nolayout = $this->_request->getParam('nolayout');
        $this->view->inlineScriptMin()->loadGroup('cart')
            ->appendScript("Pet.loadView('Cart');");
    }

    public function addAction() {
        $product_id = $this->_request->getParam('product_id');
        $is_gift = $this->_request->getParam('is_gift');
        $promo_code = $this->_request->getParam('promo_code');
        $promo_msg = null;
        if ($this->_cart_mapper->addProductById($product_id, $is_gift, $product_id)) {
            $product_msg = $this->_cart_mapper->getMessage();
            if ($promo_code && $this->_cart_mapper->addPromo($promo_code)) {
                $promo_msg = "The Promo code \"$promo_code\" will be applied " .
                    'when you get to the Checkout page';
            }
        } else {
            $product_msg = $this->_cart_mapper->getMessage();
        }
        $this->_messenger->addMessage($product_msg);
        if ($promo_msg) {
            $this->_messenger->addMessage($promo_msg);
        }
        $this->_helper->Redirector->gotoSimple('index');
    }
    
    public function addPromoAction() {
        if ($this->_request->isXMLHttpRequest()) {
            $json = $this->_request->getParam('model');
            $model = Zend_Json::decode($json);
            $code = (isset($model['code']) ? $model['code'] : '');
            $success = $this->_cart_mapper->addPromo($code);
            $this->_helper->json(array(
                'message' => $this->_cart_mapper->getMessage(),
                'success' => (int) $success
            ));
        } else {
            $code = $this->_request->getParam('code');
            $this->_cart_mapper->addPromo($code);
            $this->_helper->Redirector->setGotoSimple('index');
        }
    }

    public function setQtyAction() {
        $product_id = $this->_request->getParam('key');
        $qty = (int) $this->_request->getParam('qty');
        $this->_cart_mapper->setProductQty($key, $qty);
        $this->_messenger->addMessage('Quantity updated');
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function removeAction() {
        $key = $this->_request->getParam('key');
        $this->_cart_mapper->removeProduct($key);
        $this->_messenger->addMessage('Product removed');
        $this->_helper->Redirector->setGotoSimple('index');
    }
    
    public function redeemGiftAction() {
        $token = $this->_request->getParam('token');
        if ($this->_cart_svc->redeemGift($token)) {
            $this->_messenger->setNamespace('checkout');
            $msg = 'Welcome! To begin your gift subscription, please fill ' .
                   'out the form below.';
            $this->_messenger->addMessage($msg);
            $this->_helper->Redirector->setGotoRoute(array(), 'checkout');
        }
        // Fallback will show a "your gift was not redeemed" message
    }

    public function resetAction() {
        $this->_cart_mapper->reset();
        $this->_helper->Redirector->setGotoSimple('index');
    }

    public function test1Action() {
        $cart = $this->_cart_mapper->get();
        echo '<pre>';
        print_r($cart);
        print_r($cart->getTotals());
        foreach ($cart->products as $product) {
            echo $product->name . ' ' . $product->qty . "\n";

        }
        echo "\nsubscription: " . $cart->products->hasSubscription() . "\n";
        echo 'download: ' . $cart->products->hasDownload();
        echo '</pre>';
        exit;
    }

    public function test2Action() {
    }

}
