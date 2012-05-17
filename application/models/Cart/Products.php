<?php
/**
 * Cart products iterator
 * 
 */
class Model_Cart_Products implements Iterator, Countable {
    
    /**
     * @var int
     * 
     */
    private $_position = 0;
    
    /**
     * An array of Cart_Product objects
     * 
     */
    private $_data = array();
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_position = 0;
    }

    /**
     * @var int $id
     * @return Model_Cart_Product
     * 
     */
    public function add(Model_Cart_Product $product) {
        $this->_data[] = $product;
    }
    
    /**
     * @var int $id
     * @var return void
     * 
     */
    public function remove($id) {
        foreach ($this->_data as $k => $product) {
            if ($product->product_id == $id) {
                unset($this->_data[$k]);
                $this->_data = array_values($this->_data);
                $this->rewind();
                return;
            }
        }
    }
    
    /**
     * @var int $id
     * @var int $qty
     * @var return void
     * 
     */
    public function setQty($id, $qty) {
        foreach ($this->_data as $k => $product) {
            if ($product->product_id == $id) {
                $this->_data[$k]->qty = $qty;
                return;
            }
        }
    }
    
    /**
     * @var int $id
     * @var return void
     * 
     */
    public function incrementQty($id) {
        foreach ($this->_data as $k => $product) {
            if ($product->product_id == $id) {
                $this->_data[$k]->qty++;
                return;
            }
        }
    }

    /**
     * @var int $id
     * @return Model_Cart_Product
     * 
     */
    public function getById($id) {
        foreach ($this->_data as $k => $product) {
            if ($product->product_id == $id) {
                return $this->_data[$k];
            }
        }
    }

    /**
     * @return void
     * 
     */
    function rewind() {
        $this->_position = 0;
    }
    
    /**
     * @return Model_Cart_Product
     * 
     */
    function current() {
        return $this->_data[$this->_position];
    }
    
    /**
     * @return int
     * 
     */
    function key() {
        return $this->_position;
    }
    
    /**
     * @return void
     * 
     */
    function next() {
        ++$this->_position;
    }
    
    /**
     * @return bool
     * 
     */
    function valid() {
        return isset($this->_data[$this->_position]);
    }
    
    /**
     * @return int
     * 
     */
    public function count() {
        return count($this->_data);
    }
    
    /**
     * @return array
     * 
     */
    public function getIds() {
        $ids = array();
        foreach ($this->_data as $k => $product) {
            $ids[] = $product->product_id;
        }
        return $ids;
    }
}
