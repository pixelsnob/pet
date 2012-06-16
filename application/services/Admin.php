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
        $params = $this->initDateRangeParams($request);
        $params['sort'] = $request->getParam('sort', $id_column);
        $params['sort_dir'] = $request->getParam('sort_dir', 'desc');
        return $params;
    }
    
    public function initDateRangeParams(Zend_Controller_Request_Abstract $request) {
        $params = $request->getParams();
        if (isset($params['start_date']) && !strlen(trim($params['start_date']))) {
            $start_date = new DateTime('2000-01-01');
        } elseif (isset($params['start_date']) && strlen(trim($params['start_date']))) {
            $start_date = new DateTime($params['start_date']); 
        } else {
            $start_date = new DateTime;
            $start_date->sub(new DateInterval('P1Y'));
        }
        $params['start_date'] = $start_date->format('Y-m-d');
        $end_date = (isset($params['end_date']) && strlen(trim($params['end_date'])) ?
            $params['end_date'] : null);
        $end_date = new DateTime($end_date);
        $params['end_date'] = $end_date->format('Y-m-d');
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
    public function getCsvAsString($data, $caps = false) {
        $fp = fopen('php://temp/maxmemory:'. (12 * 1024 * 1024), 'r+');
        if ($caps) {
            stream_filter_append($fp, 'string.toupper', STREAM_FILTER_WRITE);
        }
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
