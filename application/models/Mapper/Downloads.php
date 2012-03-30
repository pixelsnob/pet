<?php
/**
 * @package Model_Mapper_Downloads
 * 
 */
class Model_Mapper_Downloads extends Pet_Model_Mapper_Abstract {

    public function __construct() {
        $this->_downloads = new Model_DbTable_Downloads;
    }

    public function getByProductId($product_id) {
        $download = $this->_downloads->getByProductId($product_id);
        if ($download) {
            return new Model_Download($download->toArray());
        }
    }
}

