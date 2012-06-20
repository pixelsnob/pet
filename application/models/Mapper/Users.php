<?php
/**
 * @package Model_Mapper_Users
 * 
 */
class Model_Mapper_Users extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_users = new Model_DbTable_Users;
    }
    
    /**
     * @param string $username
     * @return void|Model_User
     */
    public function getByUsername($username) {
        $user = $this->_users->getByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $username
     * @return void|Model_User
     */
    public function getActiveByUsername($username) {
        $user = $this->_users->getActiveByUsername($username);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $email
     * @return void|Model_User
     */
    public function getActiveByEmail($email) {
        $user = $this->_users->getActiveByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param string $email
     * @return void|Model_User
     * 
     */
    public function getByEmail($email) {
        $user = $this->_users->getByEmail($email);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /**
     * @param int $id
     * @return void|Model_User
     * 
     */
    public function getById($id) {
        $user = $this->_users->getById($id);
        if ($user) {
            return new Model_User($user->toArray());
        }
    }

    /** 
     * Builds a query out of search params and paginates the results
     * 
     * @param array $params
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $sel = $this->_users->select();
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->addDateRangeToSelect($sel, 'date_joined', $params);
        if (isset($params['search']) && $params['search']) {
            // If it's a number, try the user id, otherwise, try other text
            // fields
            if (is_numeric($params['search'])) {
                $sel->where('id = ?', $params['search']);
            } else {
                // Split search term by whitespace
                $search_parts = explode(' ', $params['search']);
                foreach ($search_parts as $v) {
                    $search = $db->quote('%' . $v . '%');
                    $where = "email like $search or first_name like $search " .
                        "or last_name like $search or username like $search";
                    $sel->where($where);

                }
            }
        }
        $this->addSortToSelect($sel, 'id', 'desc', $params);
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
        $users = array();
        foreach ($paginator as $row) {
            $users[] = new Model_User($row);
        }
        return array('paginator' => $paginator, 'data' => $users);
    }

    /**
     * @param array $data
     * @param int $id User id
     * @return int Num rows updated
     */
    public function updatePersonal(array $data, $id) {
        $user = new Model_User($data);
        $user_array = array(
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'username'   => $user->username,
            'email'      => $user->email
        );
        return $this->_users->update($user_array, $id);
    }

    /**
     * @param string $email
     * @return int Num rows updated
     * 
     */
    public function updateEmail($email, $id) {
        return $this->_users->update(array('email' => $email), $id);
    }

    /**
     * @param string $pw
     * @param int $id User id
     * @return int Num rows updated
     * 
     */
    public function updatePassword($pw, $id) {
        return $this->_users->update(array('password' => $pw), $id);
    }
    
    /**
     * @param int $id User id
     * @return int user_id 
     * 
     */
    public function updateLastLogin($id) {
        return $this->_users->update(array(
            'last_login' => date('Y-m-d H:i:s', time())), $id);
    }
    
    public function update(array $data, $id) {
        $user = new Model_User($data);
        return $this->_users->update($user->toArray(), $id);
    }

    /**
     * @param array $data
     * @param bool $is_active
     * @return int user_id
     * 
     */
    public function insert(array $data, $is_active = true) {
        $user = new Model_User($data);
        $user->is_active = (int) $is_active;
        $user->date_joined = date('Y-m-d H:i:s');
        $user_array = $user->toArray();
        unset($user_array['id']);
        return $this->_users->insert($user_array);
    }
}

