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
        $this->addElement('text', 'code', array(
            'label' => 'Code',
            'required' => false
        ))->addElement('hidden', 'sort')
          ->addElement('hidden', 'sort_dir')
          ->setElementFilters(array('StringTrim'));
    }

}
