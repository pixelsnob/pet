<?php

class Model_DbTable_UserProfiles extends Zend_Db_Table_Abstract {
    
    /**
     * @var string
     * 
     */
    protected $_name = 'user_profiles';
    
    /**
     * @param int $id
     * @return Zend_Db_Table_Row Object
     * 
     */
    public function getByUserId($id) {
        $sel = $this->select()
            ->where('user_id = ?', $id);
        return $this->fetchRow($sel);
    }
    
    /**
     * @param array $data
     * @param int $user_id
     * @return Zend_Db_Table_Row Object
     * 
     */
    public function updateByUserId(array $data, $user_id) {
        $where = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
        return parent::update($data, $where);
    }
}
