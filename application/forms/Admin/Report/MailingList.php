<?php
/**
 * Mailing list report form
 * 
 */
class Form_Admin_Report_MailingList extends Pet_Form {
    
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        $this->date_range->removeElement('end_date');
        $start_date = new DateTime;
        $this->date_range->start_date->setOptions(array(
            'class' => 'datepicker datepicker-no-max',
            'label' => 'Valid as of',
            'value' => $start_date->format('Y-m-d')
        ));
        
    }
}
