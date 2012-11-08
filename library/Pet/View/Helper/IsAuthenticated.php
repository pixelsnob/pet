<?php
/**
 * @package Pet_View_Helper_FullUrl
 * 
 */
class Pet_View_Helper_IsAuthenticated extends Zend_View_Helper_Abstract {
    
    /**
     * @param bool $is_superuser
     * @return bool
     * 
     */
    public function isAuthenticated($is_superuser = false) {
        $users_svc = new Service_Users;
        return $users_svc->isAuthenticated($is_superuser);
    }

}
