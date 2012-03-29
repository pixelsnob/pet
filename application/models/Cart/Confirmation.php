<?php
/**
 * @package Model_Cart_Confirmation
 * 
 */
class Model_Cart_Confirmation extends Onone_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'cart'      => null, // Instance of Model_Cart
        'timestamp' => null
    );
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->updateTimestamp();
    }

    /**
     * Sets timestamp to current time
     * 
     * @return void 
     */
    public function updateTimestamp() {
        $this->_data['timestamp'] = time();
    }

    /**
     * @return array
     * 
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * Clone properties that are objects
     * 
     * @return void
     */
    public function __clone() {
        if (is_object($this->_data['cart'])) {
            $this->_data['cart'] = clone $this->_data['cart'];
        }
    }
}
