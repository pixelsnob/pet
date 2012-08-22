<?php

class Admin_OrdersController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Orders'); 
        if ($page) {
            $page->setActive();
        }
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
        $this->_admin_svc = new Service_Admin;
        $this->_orders_mapper = new Model_Mapper_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
        $this->view->inlineScriptMin()->loadGroup('admin-orders')
            ->appendScript("Pet.loadView('AdminOrders');");
    }
    
    public function indexAction() {
        $orders_mapper = new Model_Mapper_Orders;
        $request = $this->_request;
        $params = $this->_admin_svc->initSearchParams($request);
        $search_form = new Form_Admin_Search;
        if (!$search_form->isValid($params)) {
            $params = array();
        }
        $orders = $orders_mapper->getPaginatedFiltered($params);
        $this->view->paginator = $orders['paginator'];
        $this->view->orders = $orders['data'];
        $this->view->params = $params;
        $this->view->search_form = $search_form;
    }

    public function detailAction() {
        $id = $this->_request->getParam('id');
        if (!$id) {
            throw new Exception('Order id was not supplied');
        }
        $order = $this->_orders_mapper->getFullOrder($id);
        if (!$order) {
            throw new Exception("Order $id not found");
        }
        $this->view->order = $order;
        $this->view->messages = $this->_helper->FlashMessenger
            ->setNamespace('order_detail')->getMessages();
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $this->_helper->FlashMessenger->setNamespace('order_add');
        $db = Zend_Db_Table::getDefaultAdapter();
        $params                 = $this->_request->getPost();
        $cart_mapper            = new Model_Mapper_Cart;
        $cart_svc               = new Service_Cart;
        $orders_svc             = new Service_Orders;
        $orders_mapper          = new Model_Mapper_Orders;
        $products_mapper        = new Model_Mapper_Products;
        $promos_mapper          = new Model_Mapper_Promos;
        $users_mapper           = new Model_Mapper_Users;
        $profile_mapper         = new Model_Mapper_UserProfiles;
        $gateway                = new Model_Mapper_PaymentGateway;
        $op_mapper              = new Model_Mapper_OrderProducts;
        $ops_mapper             = new Model_Mapper_OrderProductSubscriptions;
        $gateway_logger         = new Model_Mapper_PaymentGateway_Logger_Orders;
        $subscriptions          = $products_mapper->getSubscriptions();
        $digital_subscriptions  = $products_mapper->getDigitalSubscriptions();
        $logger                 = Zend_Registry::get('log');
        $user_id                = $this->_request->getParam('user_id');
        if ($user_id) {
            $user = $this->_users_svc->getUser($user_id);
            if (!$user) {
                throw new Exception('User not found');
            }
            $this->view->user = $user;
            $profile = $profile_mapper->getByUserId($user_id);
            if (!$profile) {
                throw new Exception('Profile not found');
            }
            $expirations = $this->_users_svc->getExpirations($user_id);
        }
        // Reset each time, we don't need values to perist
        $cart_mapper->reset();
        $form = new Form_Admin_Order(array(
            'usersMapper'           => new Model_Mapper_Users,
            'promosMapper'          => $promos_mapper,
            'subscriptions'         => $subscriptions,
            'digitalSubscriptions'  => $digital_subscriptions,
            'cart'                  => $cart_mapper->get(),
            'userId'               => $user_id
        ));
        if ($this->_request->isGet() && isset($user)) {
            // Populate form with user/user profile info
            $form->populate(array_merge($user->toArray(), $profile->toArray()));
        } elseif ($this->_request->isPost() && $form->isValid($params)) {
            // Add order
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                if (!$cart_mapper->addProductById($form->product->getValue())) {
                    throw new Exception('Error adding product to cart'); 
                }
                $promo_code = $form->promo->promo_code->getValue();
                if ($promo_code && !$cart_mapper->addPromo($promo_code)) {
                    $form->promo->promo_code->markAsError()->addError('Promo is not valid');
                    throw new Exception('Error adding promo');
                }
                $cart = $cart_mapper->get();
                // Create order object
                $data = array_merge(
                    $form->billing->getValues(true),
                    $form->shipping->getValues(true),
                    $cart->getTotals(),
                    $form->user->getValues(true),
                    $form->info->getValues(true),
                    $form->payment->getValues(true)
                );
                $data['promo_id'] = ($cart->promo ? $cart->promo->id : null);
                $data['products'] = $cart->products->toArray();
                $order = new Model_Cart_Order($data);
                if ($form->payment->amount->getValue()) {
                    $order->total = $form->payment->amount->getValue();
                    $order->discount = 0;
                }
                if ($order->payment_method == 'bypass') {
                    $order->total = 0;
                }
                // Process payment
                if ($order->total > 0 && $order->payment_method == 'credit_card') {
                    $gateway->processSale($order);
                }
                // Insert or update user and user profile
                if ($user) {
                    $order->email = $user->email;
                    $order->user_id = $user->id;
                    $order->username = $user->username;
                    $users_mapper->updatePersonal($order->toArray(), $order->user_id);
                    $profile_mapper->updateByUserId($order->toArray(), $order->user_id);
                } else {
                    $order->password = $this->_users_svc->generateHash(
                        $order->password);
                    $order->user_id = $users_mapper->insert($order->toArray(), true);
                    $profile_mapper->insert($order->toArray());
                    if (!$order->user_id) {
                        throw new Exception('user_id not defined');
                    }
                }
                // Save order data
                $order->order_id = $orders_mapper->insert($order->toArray());
                // Save order products
                $cart_svc->saveOrderProducts($order);
                // Log user note
                if ($cart->products->hasSubscription() && $cart->products->hasRenewal()) {
                    if ($order->payment_method == 'bypass') {     
                        $this->_users_svc->addUserNote(
                            'Added renewal with no payment',
                            $order->user_id,
                            $this->_users_svc->getId()
                        );
                    } else {
                        $this->_users_svc->addUserNote(
                            'Added renewal',
                            $order->user_id,
                            $this->_users_svc->getId()
                        );
                    }
                }
                // Save payments
                if ($order->total > 0) {
                    $cart_svc->saveOrderPayments($order,
                        $gateway->getSuccessfulResponseObjects());
                }
                $gateway_logger->insert(
                    true,
                    $order->toArray(),
                    $gateway->getRawCalls()
                );
                $db->commit();
                try {
                    $orders_svc->sendOrderEmail($order->order_id);
                } catch (Exception $e) {
                    $logger->log("Email for order {$order->order_id} not sent: " .
                        $e->getMessage(), Zend_Log::CRIT);                   
                }
                $this->_helper->FlashMessenger->setNamespace('order_detail')
                    ->addMessage('Order added');
                $this->_helper->Redirector->gotoSimple('detail', 'orders', 'admin',
                    array('id' => $order->order_id));
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage($e->getMessage());
                // These should fail silently if they do fail
                try {
                    $gateway->voidCalls();
                    // $order might not exist here...
                    $order_array = (isset($order) ? $order->toArray() : array());
                    // Log
                    $gateway_logger->insert(
                        false,
                        $order_array,
                        $gateway->getRawCalls(),
                        array($e->getMessage() . ' -- ' . $e->getTraceAsString())
                    );
                } catch (Exception $e1) {}
            }
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage('Please check your information');
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->order_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }
    
    /** 
     * Returns a product's cost by product id, json output
     * 
     */
    public function productPriceAction() {
        $products_mapper = new Model_Mapper_Products;
        $id = $this->_request->getQuery('id');
        $product = $products_mapper->getById($id);
        $cost = ($product && isset($product->cost) ? $product->cost : 0);
        $this->_helper->json(array('cost' => number_format($cost, 2)));
    }
}
