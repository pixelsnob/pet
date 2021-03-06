<?php

class Admin_PaymentsController extends Zend_Controller_Action {

    public function init() {
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
        $this->_admin_svc = new Service_Admin;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $params = $this->_admin_svc->initSearchParams($this->_request, 'order_id');
        $search_form = new Form_Admin_Search;
        if (!$search_form->isValid($params)) {
            $params = array();
        }
        $op_mapper = new Model_Mapper_OrderPayments;
        $payments = $op_mapper->getPaginatedFiltered($params);
        $this->view->paginator = $payments['paginator'];
        $this->view->payments = $payments['data'];
        $this->view->params = $params;
        $this->view->search_form = $search_form;
        $this->view->inlineScriptMin()->loadGroup('admin-payments')
            ->appendScript("Pet.loadView('AdminPayments');");
    }

    public function detailAction() {
        $id = $this->_request->getParam('id');
        $op_mapper = new Model_Mapper_OrderPayments;
        if (!($payment = $op_mapper->getById($id))) {
            throw new Exception('Payment not found');
        }
        $this->view->payment = $payment;
    }

    public function creditAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $payments_mapper = new Model_Mapper_OrderPayments;
        $orders_mapper   = new Model_Mapper_Orders;
        $gateway_mapper  = new Model_Mapper_PaymentGateway;
        $gateway_logger  = new Model_Mapper_PaymentGateway_Logger_Credits;
        $orders_svc      = new Service_Orders;
        $logger          = Zend_Registry::get('log');
        $params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        if (!($payment = $payments_mapper->getById($id))) {
            throw new Exception('Payment not found');
        }
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('detail', 'orders', 'admin',
                array('id' => $payment->order_id));
        }
        if (!($order = $orders_mapper->get($payment->order_id))) {
            throw new Exception('Order not found');
        }
        $this->view->payment = $payment;
        if ($payment->amount <= 0) {
            throw new Exception('Cannot credit credits: redundant!');
        }
        $form = new Form_Admin_CreditPayment(array(
            'origAmount' => $payment->amount,
            'orderPaymentId'  => $payment->order_payment_id
        ));
        $this->view->credit_form = $form;
        $payment_type = $payment->payment_type_id;
        if ($this->_request->isPost() && $form->isValid($params)) {
            try {
                $db->beginTransaction();
                $gateway_mapper->processCredit($payment->pnref,
                    $form->amount->getValue()); 
                $gateway_responses = $gateway_mapper->getSuccessfulResponseObjects();
                if (isset($gateway_responses[0])) {
                    $response = $gateway_responses[0];
                    $amount = $form->amount->getValue();
                    $payments_mapper->insert(array( 
                        'order_id'            => $payment->order_id,
                        'amount'              => -$amount,
                        'payment_type_id'     => $payment->payment_type_id,
                        'pnref'               => $response->pnref,
                        'ppref'               => $response->ppref,
                        'correlationid'       => $response->correlationid,
                        'date'                => date('Y-m-d H:i:s')
                    ));
                } else {
                    throw new Exception('No gateway responses');
                }
                $gateway_logger->insert(
                    true,
                    $payment->toArray(), 
                    $gateway_mapper->getRawCalls()
                );
                $this->_users_svc->addUserNote(
                    'Processed credit for $' . number_format($amount, 2),
                    $order->user_id,
                    $this->_users_svc->getId()
                ); 
                $db->commit();
                try {
                    $orders_svc->sendOrderCreditEmail($payment->order_id, $amount);
                } catch (Exception $e) {
                    $logger->log("Credit email for order {$order->order_id} not sent: " .
                        $e->getMessage(), Zend_Log::CRIT);                   
                }
                $this->_helper->FlashMessenger->setNamespace('order_detail')
                    ->addMessage('Payment was credited successfully');
                $this->_helper->Redirector->gotoSimple('credit-success');
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage($e->getMessage());
                $gateway_logger->insert(
                    false,
                    $payment->toArray(), 
                    $gateway_mapper->getRawCalls(),
                    array($e->getMessage() . ' ' . $e->getTraceAsString())
                );
            }
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->inlineScriptMin()->loadGroup('admin-payments')
            ->appendScript("Pet.loadView('AdminPayments');");
    }
    
    public function creditSuccessAction() {
         
    }

}
