<?php

class Admin_OrdersController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->Layout->setLayout('admin');
        $this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $page = $this->_request->getParam('page');
        $params = $this->_request->getParams();
        $orders = $this->_orders_svc->getPaginatedFiltered($params);
        $this->view->paginator = $orders['paginator'];
        $this->view->orders = $orders['data'];
        $search_form = $this->_orders_svc->getSearchForm();
        if ($search_form->isValid($params)) {
            
        }
        $this->view->params = $this->_request->getQuery();
        $this->view->search_form = $search_form;
    }

}
