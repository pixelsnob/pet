<?php
/**
 * @package Model_Mapper_Downloads
 * 
 */
class Model_Mapper_Downloads extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_downloads = new Model_DbTable_Downloads;
    }

    /**
     * @param int $product_id
     * @return null|Model_Download
     * 
     */
    public function getByProductId($product_id) {
        $download = $this->_downloads->getByProductId($product_id);
        if ($download) {
            return new Model_Download($download->toArray());
        }
    }

    /**
     * @param array $data
     * @param int $product_id
     * @return void
     * 
     */
    public function updateByProductId($data, $product_id) {
        $download_model = new Model_Download($data);
        $download = $download_model->toArray();
        unset($download['id']);
        unset($download['product_id']);
        $this->_downloads->updateByProductId($download, $product_id); 
    }

    /**
     * @param array $data
     * @return int product_id
     * 
     */
    function insert(array $data) {
        $download = new Model_Download($data);
        $dl_array = $download->toArray();
        unset($dl_array['id']);
        return $this->_downloads->insert($dl_array);
    }
}

