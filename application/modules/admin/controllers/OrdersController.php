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
        $orders = $this->_orders_svc->getPaginatedFiltered(
            $this->_request->getParams());
        $this->view->paginator = $orders['paginator'];
        $this->view->orders = $orders['data'];
    }

}
