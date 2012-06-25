<?php

class Admin_UsersController extends Zend_Controller_Action {

    public function init() {
        // Highlights nav item for all actions in controller
        $page = $this->view->navigation()->findOneByLabel('Users'); 
        if ($page) {
            $page->setActive();
        }
        if ($this->_helper->Layout->getLayout() != 'nolayout') {
            $this->_helper->Layout->setLayout('admin');
        }
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
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
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
            'mapper'   => $this->_users_mapper,
            'mode'     => 'edit'
        ));
        // Get expiration, if any
        $exp = $ops_mapper->getUnexpiredByUserId($id);
        if ($exp) {
            $form->expiration->setValue($exp->expiration);
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
                if ($form->change_password->getValue() == '1') {
                    $enc_pw = $this->_users_svc->generateHash($form->user->password->getValue());
                    $this->_users_mapper->updatePassword($enc_pw, $id);
                }
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
        $this->view->inlineScriptMin()->loadGroup('admin-users')
            ->appendScript("Pet.loadView('Admin'); Pet.loadView('AdminUsers');");
    }

    public function addAction() {
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('index');
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $params = $this->_request->getPost();
        $profiles_mapper = new Model_Mapper_UserProfiles;
        $form = new Form_Admin_User(array(
            'mapper'  => $this->_users_mapper,
            'mode'    => 'add'
        ));
        $form->submit->setLabel('Add');
        $this->view->show_pw_fields = true;
        if ($this->_request->isPost() && $form->isValid($params)) {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                $params['password'] = $this->_users_svc->generateHash(
                    $params['password']);
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
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->user_form = $form; 
        $this->view->inlineScriptMin()->loadGroup('admin-users')
            ->appendScript("Pet.loadView('Admin'); Pet.loadView('AdminUsers');");
        $this->_helper->ViewRenderer->render('form');
    }

    public function changePasswordAction() {
        $params = $this->_request->getParams();
        $users_mapper = new Model_Mapper_Users;
        $id = $this->_request->getParam('id');
        if (!$id) {
            throw new Exception('User id not supplied');
        }
        $user = $this->_users_svc->getUser($id);
        if (!$user) {
            throw new Exception('User not found');
        }
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('edit', 'users', 'admin',
                array('id' => $id));
        }
        $this->view->user = $user;
        $pw_form = new Form_Admin_ChangePassword; 
        if ($this->_request->isPost() && $pw_form->isValid($params)) {
            $hash = $this->_users_svc->generateHash(
                $pw_form->new_password->getValue());
            $users_mapper->updatePassword($hash, $id);
            $this->_helper->FlashMessenger->addMessage('Password changed');
            $this->_helper->Redirector->gotoSimple('edit', 'users', 'admin',
                array('id' => $id));
        }
        $this->view->pw_form = $pw_form;
    }
}
