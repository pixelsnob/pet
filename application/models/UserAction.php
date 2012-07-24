<?php
/**
 * @package Model_UserAction
 * 
 */
class Model_UserAction extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'user_id' => null,
        'rep_user_id' => null,
        'action' => null,
        'date_created' => null,
        'rep_user_name' => null,
        'user_name' => null
    );

    /** 
     * @return array
     * 
     */
    public function toArray() {
        $data = $this->_data;
        unset($data['user_name']);
        unset($data['rep_user_name']);
        return $data;
    }
    
}

