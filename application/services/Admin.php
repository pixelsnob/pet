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
        $date = new DateTime;
        $params['end_date'] = $request->getParam('end_date',
            $date->format('Y-m-d'));
        $date->sub(new DateInterval('P1Y'));
        $params['start_date'] = $request->getParam('start_date',
            $date->format('Y-m-d'));
        $params['sort'] = $request->getParam('sort', $id_column);
        $params['sort_dir'] = $request->getParam('sort_dir', 'desc');
        return $params;
    }
}
