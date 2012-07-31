<?php
/**
 * Returns $this->view->body_id if set
 * 
 * @package Pet_View_Helper_BodyId
 * 
 */
class Pet_View_Helper_BodyId extends Zend_View_Helper_Abstract {
    
    /**
     * @return string
     * 
     */
    public function bodyId() {
        return ($this->view->body_id ? $this->view->body_id : '');
    }
}
