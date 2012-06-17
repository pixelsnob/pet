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
        $this->date_range->start_date->setOptions(array(
            'class' => 'datepicker-no-max',
            'label' => 'Expiration'
        ));
        
    }
}
