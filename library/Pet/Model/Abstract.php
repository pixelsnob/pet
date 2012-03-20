<?php
/**
 * @package Pet_Model_Abstract 
 * 
 * 
 */
abstract class Pet_Model_Abstract {
    
    /**
     * @var array An array representation of a model
     * 
     * 
     */
    protected $_data = array();

    /**
     * @param mixed $data An array or object containing data to set
     * @return void 
     * 
     */
    public function __construct($data = null) {
        $this->setData($data);
    }
    
    /**
     * Sets data en masse.
     *    
     * @param mixed $data An array or object containing data to set
     * @return void 
     * @throws Exception
     * 
     */
    public function setData($data) {
        if (empty($data)) {
            return;
        }
        if (is_object($data) || is_array($data)) {
            foreach ($this->_data as $field => $value) {
                if (array_key_exists($field, $data)) {
                    $temp_val = $data[$field];
                } else {
                    $temp_val = $value;
                }
                $this->__set($field, $temp_val);
            }
        } else {
            throw new Exception('The entity "' . get_class($this) .
                '" must be created by an array or object.');
        }
    }
    
    /**
     * @return array Raw array of model data
     * 
     */
    public function toArray() {
        $out = array();
        foreach($this->_data as $field => $value) {
            $out[$field] = $this->__get($field);
        }
        return $out;
    }

    /**
     * @param string $field Name of field to set
     * @param mixed $value Value of field to set
     * @return void
     * 
     */
    public function __set($field, $value) {
        if (array_key_exists($field, $this->_data)) {
            $method = 'set' . str_replace('_', '', $field);
            if (method_exists(get_class($this), $method)) {
                $this->_data[$field] = call_user_func(array($this, $method), $value);
                return;
            }
            $this->_data[$field] = $value;
        }
    }

    /**
     * @param string $field Name of field to get
     * @return mixed Field value
     * 
     */
    public function __get($field) {
        $method = 'get' . str_replace('_', '', $field);
        if (array_key_exists($field, $this->_data)) {
            /*if (method_exists(get_class($this), $method)) {
                return call_user_func(array($this, $method));
            }*/
            return $this->_data[$field];
        } elseif (method_exists(get_class($this), $method)) {
            return call_user_func(array($this, $method));

        }
    }

    /**
     * Converts field name containing underscores to camel case, to match
     * up with any defined setter methods.
     * 
     * @param string $field Name of field to convert
     * @return string Field name converted to camel case
     * 
     */
    private function _fieldNameToCamelCase($field) {
        $field_parts = explode('_', $field);
        $out = '';
        foreach ($field_parts as $part) {
            $out .= ucfirst($part);
        }
        return $out;
    }
}
