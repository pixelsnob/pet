<?php
/**
 * Transactions report form
 * 
 */
class Form_Admin_Report_Transactions extends Pet_Form {
    
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        $start_date = new DateTime;
        $start_date = $start_date->sub(new DateInterval('P1D'));
        $this->date_range->start_date->setValue($start_date->format('Y-m-d'));
        $end_date = new DateTime;
        $end_date = $end_date->sub(new DateInterval('P1D'));
        $this->date_range->end_date->setValue($end_date->format('Y-m-d'));
    }
}
