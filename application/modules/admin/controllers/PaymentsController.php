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

    public function logoutAction() {
        if ($this->_users_svc->isAuthenticated()) {
            $this->_users_svc->logout(); 
        }
        $this->_helper->Redirector->gotoSimple('index');
    }

}
