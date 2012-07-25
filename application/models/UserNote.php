<?php
/**
 * @package Model_UserNote
 * 
 */
class Model_UserNote extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'user_id' => null,
        'rep_user_id' => null,
        'note' => null,
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

