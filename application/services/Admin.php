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
     * @param Zend_Controller_Request_Abstract $request
     * 
     */
    public function initDateRangeParams(Zend_Controller_Request_Abstract $request) {
        $params = $request->getParams();
        $date = new DateTime;
        $params['end_date'] = $request->getParam('end_date',
            $date->format('Y-m-d'));
        $date->sub(new DateInterval('P1Y'));
        $params['start_date'] = $request->getParam('start_date',
            $date->format('Y-m-d'));
        return $params;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @param string $id_column The id column name for sorting
     * @return array
     * 
     */
    public function initSortParams(Zend_Controller_Request_Abstract $request,
                                   $id_column = 'id') {
        $params = $request->getParams();
        $params['sort'] = $request->getParam('sort', $id_column);
        $params['sort_dir'] = $request->getParam('sort_dir', 'desc');
        return $params;
    }

    public function initSearchParams(Zend_Controller_Request_Abstract $request,
                                     $id_column = 'id') {
        
        return array_merge(
            $this->initSortParams($request, $id_column),
            $this->initDateRangeParams($request)
        );
    }

}
