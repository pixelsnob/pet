<?php
/**
 * @package Pet_View_Helper_Truncate
 * 
 */
class Pet_View_Helper_Truncate extends Zend_View_Helper_Abstract {
    
    /**
     * Truncates a string. Adds a postfix if truncated
     * 
     * @param string $string The string to truncate
     * @param int $start Starting index
     * @param int $length Truncate if string is longer than this int
     * @param string $postfix String to append if truncated
     * @return string Original or truncated string
     */
    public function truncate($string, $start = 0, $length = 100,
                             $postfix = '...') {
        $truncated_string = trim($string);
        $start = (int) $start;
        $length = (int) $length;
        // Return original string if max length is 0
        if ($length < 1) {
            return $truncated_string;
        }
        $full_length = iconv_strlen($truncated_string);
        // Truncate if necessary
        if ($full_length > $length) {
            // Right-clipped
            if ($length + $start > $full_length) {
                $start = $full_length - $length;
                $postfix = '';
            }
            // Left-clipped
            if ($start == 0) $prefix = '';
            // Truncate
            $truncated_string = trim(substr($truncated_string, $start,
                $length)) . $postfix;
        }
        return $truncated_string;
    }
}
