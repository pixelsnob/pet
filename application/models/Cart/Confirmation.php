<?php
/**
 * Cart confirmation model
 * 
 */
class Model_Cart_Confirmation extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'cart' => null,  // Model_Cart
        'order' => null, // Model_Cart_Order
        'timestamp' => null
    );

}
