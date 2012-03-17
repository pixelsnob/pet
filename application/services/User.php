<?php
/**
 * User service layer
 *
 * @package Service_User
 * 
 */
class Service_User {
    
    /**
     * @param $data Username and password, etc.
     * @return bool Auth status
     * 
     * 
     */
    public function authenticate($data) {
        $auth_adapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter());
        $auth_adapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('sha1(?)')
            ->setIdentity($data['username'])
            ->setCredential($data['password']);
        $auth = Zend_Auth::getInstance();
        if ($auth->authenticate($auth_adapter)->isValid()) {
            $storage = $auth->getStorage();
            $storage->write($auth_adapter->getResultRowObject(array(
                'user_id',
                'user_role',
            )));
            return true;
        }
        return false;
    }
    
    /**
     * @return void
     * 
     */
    public function logout() {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * @return bool Auth status
     * 
     */
    public function isAuthenticated() {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    /*public function hasAccess($resource_name) {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return false;
        }
        $identity = $auth->getIdentity();
        $acl = Zend_Registry::get('acl');
        return $acl->isAllowed($identity->user_role, $resource_name);
    }*/
}
