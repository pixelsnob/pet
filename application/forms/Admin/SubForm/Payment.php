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
        $this->payment_method->setOptions(array(
            'multiOptions' => array(
                'credit_card' => 'Credit card',
                'check'       => 'Check',
                'bypass'      => 'Bypass payment'
            ),
            'value' => 'credit_card'
        ));
        $this->addElement('text', 'amount', array(
            'label' => 'Amount',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Amount is required'
                )),
                array('Callback', true, array(
                    'callback' => function($value) {
                        return preg_match('/^\d+(\.\d\d)?$/', $value);
                    },
                    'messages' => 'Amount is invalid'
                )),
                array('LessThan', true, array(
                    'max' => 1000,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->addElement('text', 'check', array(
            'label' => 'Check number',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'messages' => 'Check number is required'
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => 'Check number must be 50 characters or less'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }
    
    public function isValid($data) {
        $payment_method = (isset($data['payment_method']) ?
            $data['payment_method'] : null);
        if ($payment_method != 'credit_card') {
            $this->cc_num->clearValidators();
            $this->cc_exp_month->clearValidators();
            $this->cc_exp_year->clearValidators();
            $this->cc_cvv->clearValidators();
        }
        if ($payment_method != 'check') {
            $this->check->setRequired(false)->clearValidators(); 
        }
        if ($payment_method == 'bypass') {
            $this->amount->setRequired(false)->clearValidators(); 
        }
        return parent::isValid($data);

    }

}
