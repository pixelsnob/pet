<?php
/**
 * Sales report form
 * 
 */
class Form_Admin_Report_Sales extends Pet_Form {
    
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        $date = new DateTime;
        $date = $date->sub(new DateInterval('P1D'));
        $date_str = $date->format('Y-m-d');
        $this->date_range->start_date->setValue($date_str);
        $this->date_range->end_date->setValue($date_str);
    }
}
