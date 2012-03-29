<?php
/**
 * @package Model_CartEventCollection
 * 
 */
class Model_Cart_EventCollection extends Onone_Model_Collection_Abstract {
    
    /**
     * @param array $results The results array
     * @return void
     */
    public function __construct($results) {
        parent::__construct('Model_Cart_Event', $results);
    }
}
