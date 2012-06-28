<?php

class Admin_PaymentsController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->Layout->setLayout('admin');
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
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }

    public function creditAction() {
        $payments_mapper = new Model_Mapper_OrderPayments;
        $gateway_mapper  = new Model_Mapper_PaymentGateway;
        $params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        if (!($payment = $payments_mapper->get($id))) {
            throw new Exception('Payment not found');
        }
        if ($payment->amount <= 0) {
            throw new Exception('Cannot credit credits: redundant!');
        }
        $form = new Form_Admin_CreditPayment(array(
            'origAmount' => $payment->amount
        ));
        $this->view->credit_form = $form;
        $payment_type = $payment->payment_type_id;
        if ($this->_request->isPost() && $form->isValid($params)) {
            try {
                $gateway_mapper->processCredit($payment->pnref,
                    $form->amount->getValue()); 
                $gateway_responses = $gateway_mapper->getSuccessfulResponseObjects();
                /*if (isset($gateway_responses[0])) {
                    $response = $gateway_responses[0];
                    $payments_mapper->insert(array( 
                        'order_id'            => $payment->order_id,
                        'amount'              => - ($form->amount->getValue()),
                        'payment_type_id'     => $payment->payment_type_id,
                        'pnref'               => $response->pnref,
                        'ppref'               => $response->ppref,
                        'correlationid'       => $response->correlationid,
                        'date'                => date('Y-m-d H:i:s')
                    ));
                    exit('wow');
                } else {
                    throw new Exception('No gateway responses');
                }*/
                print_r($gateway_mapper->getRawCalls()); exit;
            } catch (Exception $e) {
                print_r($e);       
                print_r($gateway_mapper->getRawCalls()); exit;
            }
        }
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }


}
