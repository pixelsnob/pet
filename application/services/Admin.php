<?php
/**
 * Admin service layer
 *
 * @package Service_Admin
 * 
 */
class Service_Admin {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getResource('view');
    }

    /**
     * Adds date range and sort defaults to request params
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @param string $id_column The id column name for sorting
     * @return array
     * 
     */
    public function initSearchParams(Zend_Controller_Request_Abstract $request,
                                     $id_column = 'id') {
        $params = $request->getParams();
        $params['sort'] = $request->getParam('sort', $id_column);
        $params['sort_dir'] = $request->getParam('sort_dir', 'desc');
        return $params;
    }
    
    /**
     * @param mixed $data Array or iterator
     * @return void
     * 
     */
    public function outputReportCsv($data) {
        $fp = fopen('php://output', 'w');
        $header = array_keys($data[0]->toArray());
        fputcsv($fp, $header);
        foreach ($data as $row) {
            $row = $row->toArray();
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /**
     * @param mixed $data Array or iterator
     * @return void
     * 
     */
    public function getCsvAsString($data) {
        $fp = fopen('php://temp/maxmemory:'. (12 * 1024 * 1024), 'r+');
        $header = array_keys($data[0]->toArray());
        fputcsv($fp, $header);
        foreach ($data as $row) {
            $row = $row->toArray();
            fputcsv($fp, $row);
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        fclose($fp);
        return $output;
    }
}
