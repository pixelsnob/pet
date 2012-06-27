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
        $this->_orders_svc = new Service_Orders;
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
        $order = $this->_orders_svc->getFullOrder($id);
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
        $cart_svc = new Service_Cart;
        // Reset each time, we don't need values to perist
        $cart_svc->reset();
        $params                 = $this->_request->getPost();
        $orders_mapper          = new Model_Mapper_Orders;
        $products_mapper        = new Model_Mapper_Products;
        $promos_mapper          = new Model_Mapper_Promos;
        $users_mapper           = new Model_Mapper_Users;
        $profile_mapper         = new Model_Mapper_UserProfiles;
        $gateway                = new Model_Mapper_PaymentGateway;
        $ot_mapper              = new Model_Mapper_OrderTransactions;
        $op_mapper              = new Model_Mapper_OrderProducts;
        $ops_mapper             = new Model_Mapper_OrderProductSubscriptions;
        $subscriptions          = $products_mapper->getSubscriptions();
        $digital_subscriptions  = $products_mapper->getDigitalSubscriptions();
        $logger                 = Zend_Registry::get('log');
        $form = new Form_Admin_Order(array(
            'usersMapper'           => new Model_Mapper_Users,
            'promosMapper'          => $promos_mapper,
            'subscriptions'         => $subscriptions,
            'digitalSubscriptions'  => $digital_subscriptions,
            'cart'                  => $cart_svc->get()
        ));
        if ($this->_request->isPost() && $form->isValid($params)) {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                if (!$cart_svc->addProduct($form->product->getValue())) {
                    throw new Exception('Error adding product to cart'); 
                }
                $promo_code = $form->promo->promo_code->getValue();
                if ($promo_code && !$cart_svc->addPromo($promo_code)) {
                    $form->promo->promo_code->markAsError()->addError('Promo is not valid');
                    throw new Exception('Error adding promo');
                }
                $cart = $cart_svc->get();
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
                // Process payment
                if ($order->total > 0 && $order->payment_method == 'credit_card') {
                    $gateway->processSale($order);
                }
                // Insert user/user profile
                $order->password = $this->_users_svc->generateHash(
                    $order->password);
                $order->user_id = $users_mapper->insert($order->toArray(), true);
                $profile_mapper->insert($order->toArray());
                if (!$order->user_id) {
                    throw new Exception('user_id not defined');
                }
                // Save order data
                $order->order_id = $orders_mapper->insert($order->toArray());
                // Save order products
                foreach ($cart->products as $product) {
                    // Insert into order_products
                    $opid = $op_mapper->insert($product->toArray(), $order->order_id); 
                    if ($product->isSubscription() || $product->isDigital()) {
                        $term = (int) $product->term_months;
                        // If expiration is null here, DateTime defaults to today
                        $date = new DateTime;
                        $extra_days = ($cart->promo ? $cart->promo->extra_days : 0);
                        $date->add(new DateInterval("P{$term}M{$extra_days}D"));
                        $ops_mapper->insert(array(
                            'user_id'            => $order->user_id,
                            'order_product_id'   => $opid,
                            'expiration'         => $date->format('Y-m-d H:i:s')
                        ));
                    }
                }
                // Save payments
                if ($order->total > 0) {
                    $payments_mapper = new Model_Mapper_OrderPayments;
                    if ($order->payment_method == 'credit_card') {
                        $gateway_responses = $gateway->getSuccessfulResponseObjects();
                        if (isset($gateway_responses[0])) {
                            $response = $gateway_responses[0];
                            $payments_mapper->insert(array( 
                                'order_id'            => $order->order_id,
                                'amount'              => $order->total,
                                'payment_type_id'     => Model_PaymentType::PAYFLOW,
                                'pnref'               => $response->pnref,
                                'date'                => date('Y-m-d H:i:s'),
                                'cc_number'           => substr($order->cc_num, -4),
                                'cc_expiration_month' => $order->cc_exp_month,
                                'cc_expiration_year'  => $order->cc_exp_year,
                                'cvv2match'           => $response->cvv2match,
                                'ppref'               => $response->ppref,
                                'correlationid'       => $response->correlationid
                            ));
                        }
                    } elseif ($order->payment_method == 'check') {
                        $payments_mapper->insert(array( 
                            'order_id'            => $order->order_id,
                            'amount'              => $order->total,
                            'payment_type_id'     => Model_PaymentType::CHECK,
                            'check_number'        => $order->check
                        ));
                    }
                }
                // Log
                $log_data = array(
                    'type'     => 'process',
                    'order'    => $order->toArray(),
                    'order_id' => $order->order_id,
                    'user_id'  => $order->user_id
                );
                $ot_mapper->insert(
                    true,
                    $log_data,
                    $gateway->getRawCalls()
                );
                $db->commit();
                $this->_helper->FlashMessenger->setNamespace('order_detail')
                    ->addMessage('Order added');
                $this->_helper->Redirector->gotoSimple('detail', 'orders', 'admin',
                    array('id' => $order->order_id));
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage($e->getMessage());
                $log_data = array(
                    'type'     => 'process',
                    'order'    => $order->toArray(),
                    'order_id' => $order->order_id,
                    'user_id'  => $order->user_id
                );
                // These should fail silently if they do fail
                try {
                    $gateway->voidCalls();
                    // Log
                    $ot_mapper->insert(
                        false,
                        $log_data,
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
