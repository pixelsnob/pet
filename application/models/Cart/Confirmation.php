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
        'timestamp' => null
    );

}
