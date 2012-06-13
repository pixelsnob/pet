<?php

class Admin_ReportsController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        /*$page = $this->view->navigation()->findOneByLabel('Orders'); 
        if ($page) {
            $page->setActive();
        }*/
        $this->_helper->Layout->setLayout('admin');
        $this->_admin_svc = new Service_Admin;
        //$this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $this->_forward('sales');
    }

    public function salesAction() {
        $search_form = new Form_Admin_Report_Sales;
        $this->view->search_form = $search_form;
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }

    public function subscribersAction() {

    }


    public function mailingListAction() {

    }

    public function transactionsAction() {

    }


}
