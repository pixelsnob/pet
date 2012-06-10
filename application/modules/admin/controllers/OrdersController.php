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
        $params = $this->_request->getParams();
        $search_form = $this->_orders_svc->getSearchForm();
        $date = new DateTime;
        if (!isset($params['end_date']) || !$params['end_date']) {
            $params['end_date'] = $date->format('Y-m-d');
        }
        if (!isset($params['start_date']) || !$params['start_date']) {
            $date->sub(new DateInterval('P1Y'));
            $params['start_date'] = $date->format('Y-m-d');
        }
        if (!$search_form->isValid($params)) {
            $params = array();
        }
        $orders = $this->_orders_svc->getPaginatedFiltered($params);
        $this->view->paginator = $orders['paginator'];
        $this->view->orders = $orders['data'];
        //$search_form->populate($params);
        $this->view->params = $this->_request->getQuery();
        $this->view->search_form = $search_form;
        $this->view->inlineScriptMin()//->loadGroup('admin-orders')
            ->appendScript("Pet.loadView('Admin');");
    }

}
