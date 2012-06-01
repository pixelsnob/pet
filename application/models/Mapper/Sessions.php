<?php
/**
 * @package Model_Mapper_Sessions
 * 
 */
class Model_Mapper_Sessions extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_sessions = new Model_DbTable_Sessions;
    }
    
    /**
     * @param string $id Session id
     * @return Model_Session
     * 
     */
    public function get($id) {
        $session = $this->_sessions->get($id);
    }
}

