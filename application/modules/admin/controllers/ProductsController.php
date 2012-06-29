<?php

class Admin_ProductsController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Products'); 
        if ($page) {
            $page->setActive();
        }
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
        $this->_admin_svc = new Service_Admin;
        $this->_products_mapper = new Model_Mapper_Products;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
        $this->view->inlineScriptMin()->loadGroup('admin-orders')
            ->appendScript("Pet.loadView('AdminOrders');");
    }
    
    public function indexAction() {
        $request = $this->_request;
        $params = $this->_request->getParams();
        $products = $this->_products_mapper->getPaginatedFiltered($params);
        $this->view->paginator = $products['paginator'];
        $this->view->products = $products['data'];
    }

}
