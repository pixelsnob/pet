<?php
/**
 * @package Pet_View_Helper_FullUrl
 * 
 */
class Pet_View_Helper_FullUrl extends Zend_View_Helper_Abstract {
    
    /**
     * @param string $url
     * @param mixed $host
     * @return string
     * 
     */
    public function fullUrl($url, $host = null) {
        if (!$host) {
            $host = $request->getHttpHost();
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $host . $url;
        return $url;
    }

}