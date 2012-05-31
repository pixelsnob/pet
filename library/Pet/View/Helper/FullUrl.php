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
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if (!$host) {
            $host = $request->getHttpHost();
        }
        if (isset($_SERVER['HTTP_X_ORIG_PORT'])) {
            $scheme = ($_SERVER['HTTP_X_ORIG_PORT'] == '443' ? 'https' : 'http');
        } else {
            $scheme = $request->getScheme();
        }
        $url = $scheme . '://' . $host . $url;
        return $url;
    }

}
