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
        $date = new DateTime;
        $this->date_range->start_date->setOptions(array(
            'class' => 'datepicker-no-max',
            'value' => $date->format('Y-m-d')
        ));
        $this->date_range->end_date->setOptions(array(
            'class' => 'datepicker-no-max',
            'value' => $date->format('Y-m-d')
        ));
        $this->addElement('checkbox', 'opt_in', array(
            'label' => 'Opt-in',
            'class' => 'checkbox',
            'required' => true
        ))->addElement('checkbox', 'opt_in_partner', array(
            'label' => 'Sponsor opt-in',
            'class' => 'checkbox',
            'required' => true
        ))->addElement('select', 'subscriber_type', array(
            'label' => 'Subscribers',
            'multiOptions' => array(
                'all' => 'All',
                'premium' => 'Premium Subscription',
                'digital_only' => 'Digital Only'
            ),
            'required' => true
        ));
        
    }
}
