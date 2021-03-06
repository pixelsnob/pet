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
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $dlf_mapper = new Model_Mapper_DownloadFormats;
        $sz_mapper  = new Model_Mapper_SubscriptionZones;
        $sh_mapper  = new Model_Mapper_ShippingZones;
        $params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        $product = $this->_products_mapper->getById($id, false);
        if (!$product) {
            throw new Exception('Product not found');
        }
        $params['product_type_id'] = $product->product_type_id;
        $form = new Form_Admin_Product(array(
            'productsMapper'    => $this->_products_mapper,
            'productTypeId'     => $params['product_type_id'],
            'downloadFormats'   => $dlf_mapper->getAll(),
            'subscriptionZones' => $sz_mapper->getAll(),
            'shippingZones'     => $sh_mapper->getAll(),
            'mode'              => 'edit',
            'product'           => $product

        ));
        $form->populate($product->toArray());
        if ($this->_request->isPost() && $form->isValidPartial($params)) {
            $db->beginTransaction();
            try {
                $this->_products_mapper->update($params, $id); 
                $db->commit();
                $this->_helper->FlashMessenger->addMessage('Product updated');
            } catch (Exception $e) {
                $db->rollBack();
                $msg = 'There was an error updating the database';
                $this->_helper->FlashMessenger->addMessage($msg);
            }
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage('Please check your information');
        }
        if ($this->_request->isPost()) {
            $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        } else {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
        $this->view->product_form = $form;
        $this->view->product = $product;
        $this->view->product_type_id = $params['product_type_id'];
        $this->_helper->ViewRenderer->render('form'); 
    }

    /**
     * Outputs a form for each product type
     * 
     */
    public function productSubformAction() {
        $dlf_mapper = new Model_Mapper_DownloadFormats;
        $sz_mapper  = new Model_Mapper_SubscriptionZones;
        $sh_mapper  = new Model_Mapper_ShippingZones;
        $product_type_id = $this->_request->getParam('product_type_id');
        $form = new Form_Admin_Product(array(
            'productsMapper'    => $this->_products_mapper,
            'productTypeId'     => $product_type_id,
            'downloadFormats'   => $dlf_mapper->getAll(),
            'subscriptionZones' => $sz_mapper->getAll(),
            'shippingZones'     => $sh_mapper->getAll()

        ));
        $this->view->product_form = $form;
        switch ($product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                $this->_helper->ViewRenderer->render('subscription-form');
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                $this->_helper->ViewRenderer->render('digital-form');
                break;
            case Model_ProductType::PHYSICAL:
                $this->_helper->ViewRenderer->render('physical-form');
                break;
            case Model_ProductType::COURSE:
                $this->_helper->ViewRenderer->render('course-form');
                break;
            case Model_ProductType::DOWNLOAD:
                $this->_helper->ViewRenderer->render('download-form');
                break;

        }
        $this->_helper->Layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $dlf_mapper = new Model_Mapper_DownloadFormats;
        $sz_mapper  = new Model_Mapper_SubscriptionZones;
        $sh_mapper  = new Model_Mapper_ShippingZones;
        $params = $this->_request->getParams();
        $product_type_id = $this->_request->getParam('product_type_id');
        $form = new Form_Admin_Product(array(
            'productsMapper'    => $this->_products_mapper,
            'productTypes'      => $this->_products_mapper->getProductTypes(),
            'productTypeId'     => $product_type_id,
            'downloadFormats'   => $dlf_mapper->getAll(),
            'subscriptionZones' => $sz_mapper->getAll(),
            'shippingZones'     => $sh_mapper->getAll(),
            'mode'              => 'add'
        ));
        if ($this->_request->isPost() && $form->isValidPartial($params)) {
            $db->beginTransaction();
            try {
                $product_id = $this->_products_mapper->insert($params); 
                $db->commit();
                $this->_helper->FlashMessenger->addMessage('Product added');
                $this->_helper->Redirector->gotoSimple('edit', 'products', 'admin',
                    array('id' => $product_id));
            } catch (Exception $e) {
                $db->rollBack();
                $msg = 'There was an error inserting into the database';
                $this->_helper->FlashMessenger->addMessage($msg);
            }
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage('Please check your information');
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->product_type_id = $product_type_id;
        $this->view->product_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }
    
    public function deleteDialogAction() {
        $id = $this->_request->getParam('id');
        $product = $this->_products_mapper->getById($id, false);
        if (!$product) {
            throw new Exception('Product not found');
        }
        $this->view->product = $product;
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id'); 
        try {
            $this->_products_mapper->delete($id);
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
        }
    }
}
