<?php

class Admin_OrdersController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Orders'); 
        if ($page) {
            $page->setActive();
        }
        $this->_helper->Layout->setLayout('admin');
        $this->_admin_svc = new Service_Admin;
        $this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
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
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
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

}
