<?php
/**
 * Promos search filter form
 * 
 */
class Form_Admin_PromosSearch extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $date_range_form = new Form_SubForm_DateRange;
        $this->addSubform($date_range_form, 'date_range');
        $date = new DateTime;
        $date = $date->sub(new DateInterval('P1D'));
        $date_str = $date->format('Y-m-d');
        $this->date_range->start_date->setValue($date_str);
        $this->date_range->end_date->setValue($date_str);
        $this->addElement('text', 'code', array(
            'label' => 'Code',
            'required' => false
        ))->addElement('hidden', 'sort')
          ->addElement('hidden', 'sort_dir')
          ->setElementFilters(array('StringTrim'));
    }

}
