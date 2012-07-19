<?php
/**
 * @package Pet_Model_Mapper_Abstract
 * 
 * 
 */
abstract class Pet_Model_Mapper_Abstract {
    
    /**
     * @param Zend_DbTable_Select $sel
     * @param string $date_col The name of the date column to use
     * @param array $params An array of input params
     * 
     */
    public function addDateRangeToSelect($sel, $date_col, array $params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (isset($params['start_date']) && $params['start_date']) {
            $start_date = new DateTime($params['start_date']);
        } else {
            $start_date = new DateTime('2000-01-01');
        }
        $start_date->setTime(0, 0, 0);
        $start_date = $db->quote($start_date->format('Y-m-d H:i:s'));
        if (isset($params['end_date']) && $params['end_date']) {
            $end_date = new DateTime($params['end_date']);
        } else {
            $end_date = new DateTime('3000-01-01');
        }
        $end_date->setTime(23, 59, 59);
        $end_date = $db->quote($end_date->format('Y-m-d H:i:s'));
        $sel->where("$date_col between $start_date and $end_date");
    }
    
    /**
     * @param Zend_Db_Select $sel
     * @param string $default_sort Default sort column
     * @param $default_dir Default sort direction
     * @param $params array An array of input params
     * 
     */
    public function addSortToSelect($sel, $default_sort, $default_dir,
                                    array $params) {
        $sort = (isset($params['sort']) && $params['sort'] ?
            $params['sort'] : $default_sort);
        $sort_dir = (isset($params['sort_dir']) && $params['sort_dir'] ?
            $params['sort_dir'] : $default_dir);
        $sel->order($sort . ' ' . $sort_dir);
    }
}
