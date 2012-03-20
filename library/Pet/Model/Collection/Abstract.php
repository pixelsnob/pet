<?php
/**
 * @package Pet_Model_Collection_Abstract
 * 
 * 
 */
abstract class Pet_Model_Collection_Abstract implements Iterator, Countable {
    
    /**
     * @var int Result set count
     * 
     */
    protected $_count;

    /**
     * @var mixed Result set
     * 
     */
    protected $_result_set;

    /**
     * @var string Model class name
     * 
     */
    protected $_item_class;

    /**
     * @var int Current index
     * 
     */
    private $_index;
    
    /**
     * @param string $class Model class name
     * @param array $results An array of data used to populate the collection 
     * @return void 
     * 
     */
    public function __construct($class, $results) {
        if (!class_exists($class)) {
            throw Exception('The class ' . $class . ' does not exist.');
        }
        $this->_item_class = $class;
        $this->_result_set = $results;
    }
    
    /**
     * @return array Array of results
     * 
     */
    public function toArray() {
        return $this->_result_set;
    }
    
    /**
     * @return int
     * 
     */
    public function count() {
        if (null === $this->_count) {
            $this->_count = count($this->_result_set);
        }
        return $this->_count;
    }

    /**
     * @return Onone_Model_Abstract
     * 
     */
    public function current() {
        $result  = $this->_result_set[$this->_index];
        if (!$result instanceof $this->_item_class) {
            $result  = new $this->_item_class($result);
            $this->_result_set[$this->_index] = $result;
        }
        return $result;
    }
    
    /**
     * @return Onone_Model_Abstract
     * 
     */
    public function getItem($index) {
        if ($this->_result_set[$index] instanceof $this->_item_class) {
           return $this->_result_set[$index];
        }
        return new $this->_item_class($this->_result_set[$index]);
    }

    /**
     * @return int 
     * 
     */
    public function key() {
        return $this->_index;
    }

    /**
     * @return void 
     * 
     */
    public function next() {
        $this->_index++;
    }

    /**
     * @return void 
     * 
     */
    public function rewind() {
        $this->_index = 0;
    }

    /**
     * @return bool 
     * 
     */
    public function valid() {
        return isset($this->_result_set[$this->_index]);
    }
}
