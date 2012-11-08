<?php
/**
 * @package Model_Mapper_DownloadFormats
 * 
 */
class Model_Mapper_DownloadFormats extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void 
     * 
     */
    public function __construct() {
        $this->_download_formats = new Model_DbTable_DownloadFormats;
    }
    
    /**
     * @return array
     * 
     */
    public function getAll() {
        $formats = $this->_download_formats->fetchAll(
            $this->_download_formats->select()); 
        if ($formats) {
            $out = array();
            foreach ($formats as $format) {
                $out[] = new Model_DownloadFormat($format->toArray());
            }
            return $out;
        }
    }
}

