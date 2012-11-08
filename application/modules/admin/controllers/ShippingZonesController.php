<?php

class Admin_ShippingZonesController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Shipping Zones'); 
        if ($page) {
            $page->setActive();
        }
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
        $this->_users_svc = new Service_Users;
        $this->_sz_mapper = new Model_Mapper_ShippingZones;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
        $this->view->inlineScriptMin()->loadGroup('admin-shipping-zones')
            ->appendScript("Pet.loadView('AdminShippingZones');");
    }
    
    public function indexAction() {
        $this->view->shipping_zones = $this->_sz_mapper->getAll();
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
        if ($this->_request->isPost() && $form->isValid($params)) {
            try {
                $this->_sz_mapper->update($params, $id); 
                $this->_helper->FlashMessenger->addMessage('Shipping zone updated');
            } catch (Exception $e) {
                //print_r($e); exit;
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
        $form = new Form_Admin_ShippingZone;
        if ($this->_request->isPost() && $form->isValidPartial($params)) {
            try {
                $id = $this->_sz_mapper->insert($params); 
                $this->_helper->FlashMessenger->addMessage('Shipping zone added');
                $this->_helper->Redirector->gotoSimple('edit', 'shipping-zones', 'admin',
                    array('id' => $id));
            } catch (Exception $e) {
                $msg = 'There was an error inserting into the database';
                $this->_helper->FlashMessenger->addMessage($msg);
            }
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage('Please check your information');
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->shipping_zone_form = $form;
        $this->_helper->ViewRenderer->render('form'); 
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
