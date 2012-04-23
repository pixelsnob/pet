<?php
/**
 * Promo code subform
 * 
 */
class Form_SubForm_Promo extends Zend_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        $this->addElement('text', 'promo_code', array(
            'label'        => 'Promo Code',
            'required'     => false,
            'validators'   => array(
            ),
        ));
    }
    
}
