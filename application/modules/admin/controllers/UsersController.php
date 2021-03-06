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
        $this->_user_notes_mapper = new Model_Mapper_UserNotes;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
        $this->_helper->FlashMessenger->setNamespace('admin_user');
        $this->view->inlineScriptMin()->loadGroup('admin-users')
            ->appendScript("Pet.loadView('AdminUsers');");
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
        $this->view->user_notes = $this->_user_notes_mapper->getByUserId($id);
        $this->view->profile = $profile;
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
        if ($user->expiration) {
            $form->expiration->setValue($user->expiration);
            $form->subscriber_type->setValue($user->digital_only ? 'digital' :
                'premium');
            $this->view->show_expiration_fields = true;
        } else {
            $form->expiration->setRequired(false)->clearValidators();
            $form->subscriber_type->setRequired(false)->clearValidators();
        }
        $form->info->version->setRequired(false)->clearValidators();
        // Populate form
        $form->populate(array_merge($user->toArray(), $profile->toArray()));
        if ($this->_request->isPost() && $form->isValid($params)) {
            // Update
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                $this->_users_mapper->updatePersonal($params, $id);
                if ($form->change_password->getValue() == '1') {
                    $enc_pw = $this->_users_svc->generateHash(
                        $form->user->password->getValue());
                    $this->_users_mapper->updatePassword($enc_pw, $id);
                }
                $this->_users_mapper->updateIsActive(
                    $form->is_active->getValue(), $id);
                $this->_users_mapper->updateIsSuperuser(
                    $form->is_superuser->getValue(), $id);
                $profiles_mapper->updateByUserId($params, $id);
                $form_exp = $form->expiration->getValue();
                if ($user->expiration) {
                    $this->_users_mapper->updateExpiration($form_exp,
                        ($form->subscriber_type->getValue() == 'digital'), $id);
                    $this->_users_mapper->updatePreviousExpiration(
                        $user->expiration, $id);
                }
                $this->_users_svc->addUserNote('Updated profile', $id,
                    $this->_users_svc->getId());
                $db->commit();
                $this->_helper->FlashMessenger->addMessage('User updated');
            } catch (Exception $e) {
                $db->rollBack();
                $this->_helper->FlashMessenger->addMessage(
                    'An error occurred while attempting to update');
            }
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage('Please check your information');
        }
        if ($this->_request->isGet()) {
            $this->view->messages = $this->_helper->FlashMessenger
                ->getMessages();
        } else {
            $this->view->messages = $this->_helper->FlashMessenger
                ->getCurrentMessages();
        }
        $this->view->user_form = $form; 
        $this->_helper->ViewRenderer->render('form');
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
        $form->info->version->setRequired(false)->clearValidators();
        $this->view->show_pw_fields = true;
        if ($this->_request->isPost() && $form->isValid($params)) {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            try {
                $params['password'] = $this->_users_svc->generateHash(
                    $params['password']);
                $params['user_id'] = $this->_users_mapper->insert($params,
                    $form->is_active->getValue(),
                    $form->is_superuser->getValue());
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
        } elseif ($this->_request->isPost()) {
            $this->_helper->FlashMessenger->addMessage(
                'Please check your information');
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->user_form = $form; 
        $this->_helper->ViewRenderer->render('form');
    }
    
    public function addNoteAction() {
        $params = $this->_request->getParams();
        $user_id = $this->_request->getParam('user_id');
        if (!$user_id) {
            throw new Exception('User id was not supplied');
        }
        $user = $this->_users_svc->getUser($user_id);
        if (!$user) {
            throw new Exception("User $user_id not found");
        }
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('detail', 'users', 'admin',
                array('id' => $user_id));
        }
        $form = new Form_Admin_UserNote; 
        $form->user_id->setValue($user_id);

        if ($this->_request->isPost()) {
            if ($form->isValid($params)) {
                $params['rep_user_id'] = $this->_users_svc->getId();
                try {
                    $this->_user_notes_mapper->insert($params); 
                    $this->_helper->Redirector->gotoSimple('add-note-success',
                        'users', 'admin', array('user_id' => $user_id));
                } catch (Exception $e) {
                    $this->_helper->FlashMessenger->addMessage(
                        'An error occurred while attempting to add a user note');
                }
            } else {
                $this->_helper->FlashMessenger->addMessage(
                    'Please check your information');
                
            }
        }
        $this->view->messages = $this->_helper->FlashMessenger->getCurrentMessages();
        $this->view->user = $user;
        $this->view->user_note_form = $form; 
        $this->_helper->ViewRenderer->render('note-form');
    }
    
    public function addNoteSuccessAction() {
        $user_id = $this->_request->getParam('user_id');
        if (!$user_id) {
            throw new Exception('User id was not supplied');
        }
        $user = $this->_users_svc->getUser($user_id);
        if (!$user) {
            throw new Exception("User $user_id not found");
        }
        $this->view->user = $user;
    }

    public function deleteNoteDialogAction() {
        $id = $this->_request->getParam('id');
        $user_note = $this->_user_notes_mapper->getById($id, false);
        if (!$user_note) {
            throw new Exception('User note not found');
        }
        $this->view->user_note = $user_note;
        if ($this->_request->getParam('cancel')) {
            $this->_helper->Redirector->gotoSimple('detail', 'users', 'admin',
                array('id' => $user_note->user_id));
        } elseif ($this->_request->getParam('submit')) {
            try {
                $this->_user_notes_mapper->delete($id);
                $this->view->status = true;
            } catch (Exception $e) {
                $this->view->status = false;
            }
            $this->_helper->ViewRenderer->render('delete-note-results');
            return;
        }
    }

    public function deleteNoteAction() {
        $id = $this->_request->getParam('id'); 
        try {
            $this->view->status = true;
        } catch (Exception $e) {
            $this->view->status = false;
        }
    }
}
