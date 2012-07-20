<?php

class Admin_PromosController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Promos'); 
        if ($page) {
            $page->setActive();
        }
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
        $this->_users_svc = new Service_Users;
        $this->_admin_svc = new Service_Admin;
        $this->_promos_mapper = new Model_Mapper_Promos;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
        $this->view->inlineScriptMin()->loadGroup('admin-promos')
            ->appendScript("Pet.loadView('AdminPromos');");
    }
    
    public function indexAction() {
        $request = $this->_request;
        $params = $request->getParams();
        $this->view->promos = $this->_promos_mapper->getAll();
        $search_form = new Form_Admin_PromosSearch;
        if (!$search_form->isValid($params)) {
            $params = array();
        }
        $promos = $this->_promos_mapper->getPaginatedFiltered($params);
        $this->view->paginator = $promos['paginator'];
        $this->view->promos = $promos['data'];
        $this->view->params = $params;
        $this->view->search_form = $search_form;
    }

    public function editAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $products_mapper = new Model_Mapper_Products;
        $promo_products_mapper = new Model_Mapper_PromoProducts;
        $params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        $promo = $this->_promos_mapper->getById($id);
        if (!$promo) {
            throw new Exception('Promo not found');
        }
        $products = $products_mapper->getNamesGroupedByProductType();
        $promo_products = $promo_products_mapper->getByPromoId($id);
        $ppids = array();
        foreach ($promo_products as $pp) {
            $ppids[] = $pp->product_id;
        }
        $form = new Form_Admin_Promo(array(
            'promosMapper' => $this->_promos_mapper,
            'promo'        => $promo,
            'products'     => $products
        ));
        $delete_banner = $this->_request->getPost('delete_banner');;
        $form->populate($promo->toArray());
        $form->products->setValue($ppids);
        if ($promo->banner && !$delete_banner) {
            $this->view->banner = '/images/uploads/promos/' . $promo->banner;
        }
        if ($this->_request->isPost()) {
            if ($form->isValid($params)) {
                $db->beginTransaction();
                try {
                    $tmp_banner = $form->tmp_banner->getValue();
                    $banner     = $form->banner->getValue();
                    $new_banner = $this->_copyBannerUpload($banner,
                        $tmp_banner, $id);
                    if ($new_banner) {
                        $this->_promos_mapper->updateBanner($new_banner, $id);
                        $this->view->banner = '/images/uploads/promos/' .
                            $new_banner;
                    } elseif ($form->delete_banner->getValue()) {
                        $this->_promos_mapper->updateBanner(null, $id);
                    }
                    $this->_promos_mapper->update($params, $id); 
                    $product_ids = (array) $this->_request->getParam('products');
                    if (!empty($product_ids)) {
                        $promo_products_mapper->updateByPromoId(
                            $product_ids, $id);
                    }
                    $this->_helper->FlashMessenger->addMessage('Promo updated');
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $msg = 'There was an error updating the database';
                    $this->_helper->FlashMessenger->addMessage($msg);
                }
            } else {
                $this->_helper->FlashMessenger->addMessage(
                    'Please check your information');
                $tmp_banner = $form->tmp_banner->getValue();
                $banner     = $form->banner->getValue();
                if ($banner) {
                    $form->tmp_banner->setValue($banner);
                    $form->delete_banner->setValue('');
                    $this->view->banner = $this->view->url(array(
                        'action'     => 'tmp-image',
                        'controller' => 'index',
                        'filename'   => $banner
                    ));
                } elseif ($tmp_banner) {
                    $this->view->banner = $this->view->url(array(
                        'action'     => 'tmp-image',
                        'controller' => 'index',
                        'filename'   => $tmp_banner
                    ));
                }
            }
            $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        } else {
            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
        $this->view->promo_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $params = $this->_request->getParams();
        $products_mapper = new Model_Mapper_Products;
        $promo_products_mapper = new Model_Mapper_PromoProducts;
        $products = $products_mapper->getNamesGroupedByProductType();
        $form = new Form_Admin_Promo(array(
            'promosMapper' => $this->_promos_mapper,
            'products'     => $products
        ));
        if ($this->_request->isPost()) {
            if ($form->isValid($params)) {
                $db->beginTransaction();
                try {
                    // Insert
                    $id  = $this->_promos_mapper->insert($params); 
                    $product_ids = (array) $this->_request->getParam('products');
                    if (!empty($product_ids)) {
                        $promo_products_mapper->updateByPromoId(
                            $product_ids, $id);
                    }
                    // File upload stuff
                    $tmp_banner = $form->tmp_banner->getValue();
                    $banner     = $form->banner->getValue();
                    $new_banner = $this->_copyBannerUpload($banner,
                        $tmp_banner, $id);
                    if ($new_banner) {
                        $this->_promos_mapper->updateBanner($new_banner, $id);
                    }
                    $db->commit();
                    $this->_helper->FlashMessenger->addMessage('Promo added');
                    $this->_helper->Redirector->gotoSimple('edit', 'promos', 'admin',
                        array('id' => $id));
                } catch (Exception $e) {
                    $db->rollBack();
                    $msg = $e->getMessage();
                    $this->_helper->FlashMessenger->addMessage($msg);
                }
            } else {
                $tmp_banner = $form->tmp_banner->getValue();
                $banner     = $form->banner->getValue();
                if ($banner) {
                    $form->tmp_banner->setValue($banner);
                    $this->view->banner = $this->view->url(array(
                        'action'     => 'tmp-image',
                        'controller' => 'index',
                        'filename'   => $banner
                    ));
                } elseif ($tmp_banner) {
                    $this->view->banner = $this->view->url(array(
                        'action'     => 'tmp-image',
                        'controller' => 'index',
                        'filename'   => $tmp_banner
                    ));
                }
                $this->_helper->FlashMessenger->addMessage(
                    'Please check your information');
            }
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->promo_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }
    
    public function deleteDialogAction() {
        $id = $this->_request->getParam('id');
        $promo = $this->_promos_mapper->getById($id, false);
        if (!$promo) {
            throw new Exception('Shipping zone not found');
        }
        $this->view->promo = $promo;
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id'); 
        try {
            $this->_promos_mapper->delete($id);
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
        }
    }

    private function _copyBannerUpload($banner, $tmp_banner, $promo_id) {
        $config = Zend_Registry::get('app_config');
        if ($banner || $tmp_banner)  {
            $banner = ($tmp_banner ? $tmp_banner : $banner);
            $banner_path  = "/tmp/$banner";
            $new_banner = "banner-$promo_id";
            $dest_path = "{$config['image_upload_dir']}/promos/{$new_banner}";
            if (!copy($banner_path, $dest_path)) {
                throw new Exception('File upload copy failed');
            }
            return $new_banner;
        }
    }
}

