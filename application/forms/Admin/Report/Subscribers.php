<?php
/**
 * Subscribers report form
 * 
 */
class Form_Admin_Report_Subscribers extends Pet_Form {
    
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        $this->date_range->removeElement('end_date');
        $date = new DateTime;
        $this->date_range->start_date->setOptions(array(
            'class' => 'datepicker-no-max',
            'label' => 'Valid as of',
            'value' => $date->format('Y-m-d')
        ));
        $date = $date->sub(new DateInterval('P1D'));
        $date_str = $date->format('Y-m-d');
        $this->date_range->start_date->setValue($date_str);
        $this->addElement('select', 'opt_in', array(
            'label' => 'Opt-In',
            'multiOptions' => array(
                'apet' => 'APET opt-in',
                'sponsor' => 'Sponsor opt-in',
                'all' => 'All subscribers'
            ),
            //'belongsTo' => 'qty',
            'required' => true
        ))->addElement('select', 'subscriber_type', array(
            'label' => 'Subscribers',
            'multiOptions' => array(
                'all' => 'All'
            ),
            'required' => true
        ));
        
    }
}
