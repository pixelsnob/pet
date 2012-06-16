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
    }
}
