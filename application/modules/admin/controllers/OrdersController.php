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
        $this->_helper->FlashMessenger->setNamespace('admin_order');
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
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $cart_svc = new Service_Cart;
        $cart_svc->reset();
        $params                 = $this->_request->getPost();
        $orders_mapper          = new Model_Mapper_Orders;
        $products_mapper        = new Model_Mapper_Products;
        $promos_mapper          = new Model_Mapper_Promos;
        $users_mapper           = new Model_Mapper_Users;
        $profile_mapper         = new Model_Mapper_UserProfiles;
        $gateway                = new Model_Mapper_PaymentGateway;
        $ot_mapper              = new Model_Mapper_OrderTransactions;
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
                throw new Exception('fuck');
                if (!$cart_svc->addProduct($form->product->getValue())) {
                    throw new Exception('Error adding product to cart'); 
                }
                $promo_code = $form->promo->promo_code->getValue();
                if ($promo_code && !$cart_svc->addPromo($promo_code)) {
                    $form->promo->promo_code->markAsError()->addError('Promo is not valid');
                    throw new Exception('Error adding promo');
                }
                $cart = $cart_svc->get();
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
                if (!$cart->isFreeOrder() && $order->payment_method ==
                        'credit_card') {
                    $gateway->processSale($order);
                }
                if ($order->payment_method == 'bypass') {
                    $order->total = 0;
                }
                $order->password = $this->_users_svc->generateHash(
                    $order->password);
                $order->user_id = $users_mapper->insert($order->toArray(), true);
                $profile_mapper->insert($order->toArray());
                if (!$order->user_id) {
                    throw new Exception('user_id not defined');
                }
                // Save order data
                $order->order_id = $orders_mapper->insert($order->toArray());
                $cart_svc->saveOrderProducts($order);
                if (!$cart->isFreeOrder()) {
                    if ($order->payment_method == 'credit_card') {
                        $order_payment_id = $cart_svc->saveOrderPayments($order);
                    } elseif ($order->payment_method == 'check') {
                        $payments_mapper = new Model_Mapper_OrderPayments;
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
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage($e->getMessage());
                $this->view->messages = $this->_helper->FlashMessenger
                    ->getCurrentMessages();
            }
        }
        $this->view->order_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }

}
