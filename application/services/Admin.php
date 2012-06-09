<?php
/**
 * Admin service layer
 *
 * @package Service_Admin
 * 
 */
class Service_Admin {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getResource('view');
    }

    public function getPaginatedFilteredList($page = null, $filters = array()) {
        return $this->_orders->getPaginatedFilteredList($page, $filters);
    }
}
