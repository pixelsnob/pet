<?php
/**
 * @package Model_ProductType
 * 
 */
class Model_ProductType extends Pet_Model_Abstract {

    const DOWNLOAD             = 1;
    const PHYSICAL             = 2;
    const COURSE               = 3;
    const SUBSCRIPTION         = 4;
    const DIGITAL_SUBSCRIPTION = 5;
    
    public $_data = array(
        'id' => null,
        'name' => null,
        'type' => null,
        'plural_name' => null
    );

}

