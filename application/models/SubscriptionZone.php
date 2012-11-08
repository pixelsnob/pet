<?php
/**
 * @package Model_SubscriptionZone
 * 
 * 
 */
class Model_SubscriptionZone extends Pet_Model_Abstract {
    
    const CAN  = 1;
    const USA  = 2;
    const INTL = 3;

    public $_data = array(
        'id' => null,
        'name' => null,
        'zone' => null
    );

}

