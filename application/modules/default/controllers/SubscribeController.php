<?php

class SubscribeController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        $form = new Default_Form_Login;
        /*$post = $this->_request->getPost();
        if ($this->_request->isPost() and $form->isValid($post)) {
            if ($this->_user_svc->authenticate($post)) {
                $this->_helper->Redirector->gotoSimple('welcome', 'login', 'university');
            } else {
                echo 'boo';
            }   
        }
        $this->view->form = $form;*/
    }
}
