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

}
