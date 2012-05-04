<?php
/**
 * A simple payment gateway class.
 *  
 * 
 */
class PayPal {
    
    /**
     * @var array $_fields
     */
    private $_fields = array();

    /**
     * @var array $_sensitive_fields
     */
    private $_sensitive_fields = array();

    /**
     * @var string $_url
     */
    private $_url = '';

    /**
     * @var string $_raw_response
     */
    private $_raw_response = '';

    /**
     * @var array $_response
     */
    private $_response = array();

    /**
     * @var array $_headers
     */
    private $_headers = array();

    /**
     * @var string $_error
     */
    private $_error = '';

    /**
     * @return void
     */
    public function __construct() {}
    
    /**
     * Sets the URL to post to.
     * 
     * @param string $url
     * @return PayPal
     */
    public function setUrl($url) {
        $this->_url = $url;
        return $this;
    }

    /**
     * Add fields en masse via an array.
     * 
     * @paran array $fields
     * @return PayPal
     */
    public function setFields(array $fields) {
        $this->_fields = array_merge($this->_fields, $fields);
        return $this;
    }
    
    /**
     * Sets an array of sensitive fields.
     * 
     * @param array $fields
     * @return PayPal
     */
    public function setSensitiveFields(array $fields) {
        $this->_sensitive_fields = $fields;
        return $this;
    }

    /**
     * Sets a field name and value. Make sure return delimiter is filtered
     * from $value, unless specified.
     * 
     * @param string $name
     * @param string $value
     * @return PayPal
     */
    public function setField($name, $value) {
        $this->_fields[$name] = $value;
        return $this;
    }

    /**
     * Sets an HTTP header in the curl call.
     * 
     * @param string $name
     * @param string $value
     * @return PayPal
     */
    public function setHeader($name, $value) {
        $this->_headers[] = "{$name}: {$value}";
        return $this;
    }

    /**
     * Sets headers in the curl call en masse.
     * 
     * @param array $headers 
     * @return PayPal
     */
    public function setHeaders(array $headers) {
        $this->_headers = array_merge($this->_headers, $headers);
        return $this;
    }

    /**
     * Constructs a string of key/value pairs
     * 
     * @return string
     */
    public function getRequest() {
        $post = '';
        $c = 0;
        foreach ($this->_fields as $name => $value) {
            $post .= ($c > 0) ? '&' : '';
            $strlen = strlen($value);
            $post .= "{$name}[{$strlen}]={$value}";
            $c++;
        }
        return $post;
    }
    
    /**
     * Gets request fields as an array, with sensitive fields masked
     * 
     * @return array
     */
    public function getCleanedRequestAsArray() {
        $request = $this->_fields;
        $out = array();
        foreach ($this->_sensitive_fields as $f) {
            $request[$f] = 'XXXXXXXXXXXXXXXXXXX';
        }
        return $request;
    }

    /**
     * Makes a call to the gateway.
     * 
     * @return PayPal
     */
    public function send() {
        $ch = curl_init();
        // Configure curl.
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getRequest());
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        // Make sure there are no curl errors.
        $this->_raw_response = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->_error = curl_error($ch);
        }
        return $this;
    }
    
    /**
     * Returns request string free of any sensitive info, for logging, etc.
     * 
     * @return string 
     */
    public function getCleanedRequest() {
        $request = $this->getRequest();
        foreach ($this->_sensitive_fields as $field) {
            $request = preg_replace("/({$field}\[\d*\])=[^&]*/",
                "$1=XXXXXXXX", $request);
        }
        return $request;
    }

    /**
     * Returns raw response.
     * 
     * @return string 
     */
    public function getRawResponse() {
        return $this->_raw_response;
    }

    /**
     * Returns processed response.
     * 
     * @return array 
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * Returns curl error message, if any.
     * 
     * @return string
     */
    public function getError() {
        return $this->_error;
    }
    
    /**
     * Transforms raw response string into an array or
     * object.
     * 
     * return void
     */
    public function processResponse() {
        if (!strlen(trim($this->_raw_response))) {
            return;
        }
        $temp_response = explode('&', $this->_raw_response);
        foreach ($temp_response as $r) {
            $r_parts = explode('=', $r);
            $k = $r_parts[0];
            $v = '';
            if (isset($r_parts[1])) {
                $v = $r_parts[1];
            }
            $this->_response[$k] = $v;
        }
    }

    /**
     * Gets a response field by its index.
     * 
     * @param string index
     * @return string 
     */
    public function getResponseField($index) {
        if (isset($this->_response[$index])) {
            return trim($this->_response[$index]);
        }
    }
    
    /**
     * Returns bool success status.
     * 
     * @return bool 
     */
    public function isSuccess() {
        $result = $this->getResponseField('RESULT');
        if ($result === '0') {
            return true;
        }
        return false;
    }
    
}
