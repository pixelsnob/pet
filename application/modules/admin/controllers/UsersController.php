<?php

class Admin_UsersController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Users'); 
        if ($page) {
            $page->setActive();
        }
        $this->_helper->Layout->setLayout('admin');
        $this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        $this->_admin_svc = new Service_Admin;
        $this->_users_mapper = new Model_Mapper_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $params = $this->_admin_svc->initSearchParams($this->_request);
        $search_form = new Form_Admin_Search;
        if (!$search_form->isValid($params)) {
            $params = array();
        }
        $orders = $this->_users_mapper->getPaginatedFiltered($params);
        $this->view->paginator = $orders['paginator'];
        $this->view->orders = $orders['data'];
        $this->view->params = $params;
        $this->view->search_form = $search_form;
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }

    public function detailAction() {
        $orders_mapper = new Model_Mapper_Orders;
        $id = $this->_request->getParam('id');
        if (!$id) {
            throw new Exception('User id was not supplied');
        }
        $user = $this->_users_svc->getUser($id);
        if (!$user) {
            throw new Exception("User $id not found");
        }
        $this->view->user = $user;
        $profile = $this->_users_svc->getProfile($id);
        if (!$profile) {
            throw new Exception("User profile for user $id not found");
        }
        $this->view->profile = $profile;
        $this->view->expirations = $this->_users_svc->getExpirations($id);
        $this->view->orders = $orders_mapper->getByUserId($id);
    }

    public function editAction() {
        $this->view->profile_form = $this->_users_svc->getProfileForm(); 
    }
}
