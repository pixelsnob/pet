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
        $this->view->inlineScriptMin()->loadGroup('admin-products')
            ->appendScript("Pet.loadView('AdminProducts');");
    }
    
    public function indexAction() {
        $request = $this->_request;
        $params = $this->_request->getParams();
        $products = $this->_products_mapper->getPaginatedFiltered($params);
        $product_types = $this->_products_mapper->getProductTypes();
        $form = new Form_Admin_ProductsSearch(array(
            'productTypes' => $product_types
        ));
        if (!$form->isValid($params)) {
            $params = array();
        }
        $this->view->filter_form = $form;
        $this->view->params = $params;
        $this->view->paginator = $products['paginator'];
        $this->view->products = $products['data'];
    }
    
    public function editAction() {
        $dlf_mapper = new Model_Mapper_DownloadFormats;
        $id = $this->_request->getParam('id');
        $product = $this->_products_mapper->getById($id, false);
        if (!$product) {
            throw new Exception('Product not found');
        }
        $form = new Form_Admin_Product(array(
            'productTypes'    => $this->_products_mapper->getProductTypes(),
            'product'         => $product,
            'downloadFormats' => $dlf_mapper->getAll() 
        ));
        $this->view->product_form = $form;
        $this->view->product = $product;
        $this->_helper->ViewRenderer->render('form'); 
    }
}
