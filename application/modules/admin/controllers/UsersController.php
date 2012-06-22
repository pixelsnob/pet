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
        $this->_helper->FlashMessenger->setNamespace('admin_user');
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $params = $this->_request->getParams();
        $orders_mapper = new Model_Mapper_Orders;
        $profiles_mapper = new Model_Mapper_UserProfiles;
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $id = $this->_request->getParam('id');
        if (!$id) {
            throw new Exception('User id was not supplied');
        }
        // Get user and user_profile
        $user = $this->_users_svc->getUser($id);
        $profile = $this->_users_svc->getProfile($id);
        if (!$user || !$profile) {
            throw new Exception('User or user profile not found');
        }
        $this->view->user = $user;
        $form = new Form_Admin_User(array(
            'identity' => $user,
            'mapper'   => $this->_users_mapper
        ));
        // Get expiration, if any
        $exp = $ops_mapper->getUnexpiredByUserId($id);
        if ($exp) {
            $form->expiration->setOptions(array(
                'value' => $exp->expiration
                //'class' => 'datepicker-no-max'
            ));
            $form->digital_only->setValue($exp->digital_only);
            $this->view->show_expiration_fields = true;
        }
        // Populate form
        $form->populate(array_merge($user->toArray(), $profile->toArray()));
        if ($this->_request->isPost() && $form->isValid($params)) {
            // Update
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                $this->_users_mapper->updatePersonal($params, $id);
                $this->_users_mapper->updateIsActive($form->is_active->getValue(), $id);
                $profiles_mapper->updateByUserId($params, $id);
                $form_exp = $form->expiration->getValue();
                if ($exp && $form_exp) {
                    $ops_mapper->update(array(
                        'expiration'   => $form_exp,
                        'digital_only' => $form->digital_only->getValue(),
                        'user_id' => $id
                    ), $exp->id);
                } elseif ($form_exp) {
                    $ops_mapper->insert(array(
                        'expiration'   => $form_exp,
                        'digital_only' => $form->digital_only->getValue()
                    ));
                }
                $db->commit();
                $this->_helper->FlashMessenger->addMessage('User updated');
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage(
                    'An error occurred while attempting to update');
            }
            $this->view->messages = $this->_helper->FlashMessenger
                ->getCurrentMessages();
        }
        if ($this->_request->isGet()) {
            $this->view->messages = $this->_helper->FlashMessenger
                ->getMessages();
        }
        $this->view->user_form = $form; 
        $this->_helper->ViewRenderer->render('form');
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }

    public function addAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $params = $this->_request->getPost();
        $profiles_mapper = new Model_Mapper_UserProfiles;
        $form = new Form_Admin_User(array('mapper' => $this->_users_mapper));
        $form->submit->setLabel('Add');
        $form->user->setIsArray(false)->addPasswordFields();
        $this->view->show_pw_fields = true;
        if ($this->_request->isPost() && $form->isValid($params)) {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                $params['password'] = $this->_users_svc->generateHash(
                    $params['password']);
                //var_dump($form->is_active->getValue() == '1');
                $params['user_id'] = $this->_users_mapper->insert($params,
                    ($form->is_active->getValue() == '1'));
                $profiles_mapper->insert($params);
                $db->commit();
                $this->_helper->FlashMessenger->addMessage(
                    'User added');
                $this->_helper->Redirector->gotoSimple('edit', 'users', 'admin',
                    array('id' => $params['user_id']));
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage(
                    'An error occurred while attempting to add user');
            }
            $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
        $this->view->user_form = $form; 
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
        $this->_helper->ViewRenderer->render('form');
    }
}
