<?php

class Admin_OrdersController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Orders'); 
        if ($page) {
            $page->setActive();
        }
        $this->_helper->Layout->setLayout('admin');
        $this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $orders_mapper = new Model_Mapper_Orders;
        $request = $this->_request;
        $params = $request->getParams();
        $search_form = new Form_Admin_Search;
        $date = new DateTime;
        $params['end_date'] = $request->getParam('end_date',
            $date->format('Y-m-d'));
        $date->sub(new DateInterval('P1Y'));
        $params['start_date'] = $request->getParam('start_date',
            $date->format('Y-m-d'));
        $params['sort'] = $request->getParam('sort', 'id');
        $params['sort_dir'] = $request->getParam('sort_dir', 'desc');
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
