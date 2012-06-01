<?php
/**
 * Sessions service layer
 *
 * @package Service_Sessions
 * 
 */
class Service_Sessions extends Pet_Service {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_sessions = new Model_Mapper_Sessions;
    }
    
    /**
     * @param string $id Session id
     * @return 
     * 
     */
    public function get($id) {
        return $this->_sessions->get($id);
    }

}
