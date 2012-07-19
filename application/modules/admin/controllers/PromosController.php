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
        $params = $this->_admin_svc->initSearchParams($request);
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
        $params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        $zone = $this->_sz_mapper->getById($id);
        if (!$zone) {
            throw new Exception('Shipping zone not found');
        }
        $form = new Form_Admin_ShippingZone;
        $form->populate($zone->toArray());
        if ($this->_request->isPost() && $form->isValidPartial($params)) {
            try {
                $this->_sz_mapper->update($params, $id); 
                $this->_helper->FlashMessenger->addMessage('Shipping zone updated');
            } catch (Exception $e) {
                print_r($e); exit;
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
        $this->view->shipping_zone_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $params = $this->_request->getParams();
        $form = new Form_Admin_Promo;
        if ($this->_request->isPost()) {
            if ($form->isValid($params)) {
                try {
                    $id = $this->_promos_mapper->insert($params); 
                    $tmp_banner = $form->tmp_banner->getValue();
                    $banner     = $form->banner->getValue();
                    if ($banner || $tmp_banner)  {
                        $banner = ($tmp_banner ? $tmp_banner : $banner);
                        $banner_path  = "/tmp/$banner";
                        $banner_parts = explode('.', $banner_path);
                        $ext = $banner_parts[count($banner_parts) - 1];
                        $new_banner = "banner-{$id}.{$ext}";
                        $banner_dest_path = PUBLIC_PATH . "/images/promos/$new_banner";
                        if (!copy($banner_path, $banner_dest_path)) {
                            throw new Exception('File upload copy failed');
                        }
                        $this->_promos_mapper->updateBanner($new_banner, $id);
                    }
                    $this->_helper->FlashMessenger->addMessage('Promo added');
                    //$this->_helper->Redirector->gotoSimple('edit', 'promos', 'admin',
                    //    array('id' => $id));
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $this->_helper->FlashMessenger->addMessage($msg);
                }
            } else {
                $tmp_banner = $form->tmp_banner->getValue();
                $banner     = $form->banner->getValue();
                if ($banner) {
                    $form->tmp_banner->setValue($banner);
                    $this->view->banner = $this->view->url(array(
                        'action' => 'tmp-image', 'filename' => $banner));
                } elseif ($tmp_banner) {
                    $this->view->banner = $this->view->url(array(
                        'action' => 'tmp-image', 'filename' => $tmp_banner));
                }
                $this->_helper->FlashMessenger->addMessage(
                    'Please check your information');
            }
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->promo_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
    }
    
    public function tmpImageAction() {
        $filename = $this->_request->getParam('filename');
        $img = file_get_contents('/tmp/' . $filename);
        $this->_response->setHeader('Content-Type', mime_content_type($filename));
        $this->_response->setHeader('Content-Length', strlen($img));
        $this->_response->setBody($img);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();
    }

    public function deleteDialogAction() {
        $id = $this->_request->getParam('id');
        $zone = $this->_sz_mapper->getById($id, false);
        if (!$zone) {
            throw new Exception('Shipping zone not found');
        }
        $this->view->shipping_zone = $zone;
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id'); 
        try {
            $this->_sz_mapper->delete($id);
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
        }
    }

}

