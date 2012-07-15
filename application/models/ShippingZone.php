<?php
/**
 * @package Model_ShippingZone
 * 
 */
class Model_ShippingZone extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'usa' => null,
        'can' => null,
        'intl' => null
    );
    
    /**
     * @return string
     * 
     * 
     */
    public function getLabel() {
        return sprintf("$%s/$%s/$%s", $this->_data['usa'], $this->_data['can'],
            $this->_data['intl']);
    }

}

