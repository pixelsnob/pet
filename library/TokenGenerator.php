<?php
/**
 * A simple token generator
 * 
 */
class TokenGenerator {
    
    /**
     * @return void
     */
    public function __construct() {}
    
    /**
     * @param int $length
     * @return string 
     */
    public function generate($length = 32) {
        $token = '';
        for ($i = 0; $i < $length; $i++) { 
            $token .= chr(mt_rand(0, 255));
        }
        return $token;
    }

    /**
     * @param string Value to hash
     * @param int $length
     * @return string 
     */
    public function generateHash($value, $salt_length = 5) {
        $salt = '';
        for ($i = 0; $i < $salt_length; $i++) { 
            $salt .= chr(mt_rand(0, 255));
        }
        $salt = substr(sha1($salt), 0, 5);
        $hash = sha1($salt . $value);
        return ('sha1$' . $salt . '$' . $hash);
    }
}
