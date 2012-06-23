<?php
/**
 * Admin payment subform
 * 
 */
class Form_Admin_SubForm_Payment extends Form_SubForm_Payment {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->payment_method->setMultiOptions(array(
            'credit_card' => 'Credit card',
            'check'       => 'Check',
            'bypass'      => 'Bypass payment'
        ));
        $this->addElement('text', 'amount', array(
            'label' => 'Amount',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Amount is required'
                )),
                array('Digits', true, array(
                    'messages' => 'Amount is invalid'
                ))
            )
        ))->addElement('text', 'check', array(
            'label' => 'Check number',
            'validators' => array()
        ))->setElementFilters(array('StringTrim'));
    }
    
    public function isValid($data) {
        /*$payment_method = (isset($data['payment_method']) ?
            $data['payment_method'] : null);
        if ($payment_method == 'paypal') {
            $this->getElement('cc_num')->clearValidators();
            $this->getElement('cc_exp_month')->clearValidators();
            $this->getElement('cc_exp_year')->clearValidators();
            $this->getElement('cc_cvv')->clearValidators();
        }
        return parent::isValid($data);*/
    }

}
