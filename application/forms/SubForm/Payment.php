<?php
/**
 * Payment info subform
 * 
 */
class Form_SubForm_Payment extends Pet_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        $payment_opts = array(
            'credit_card' => 'Credit Card',
            'paypal'      => 'PayPal'
        );
        $this->addElement('radio', 'payment_method', array(
            'multiOptions' => $payment_opts, 
            'label'        => 'Payment Method',
            'required'     => false,
            'class'        => 'radio',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please choose your payment method.'
                ))
            ),
            'separator' => '</li><li>',
            'decorators'   => array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => 'li')),
                array(array('row' => 'HtmlTag'), array('tag' => 'ul')),
                'Errors'
            ),
            'disableLoadDefaultDecorators' => true

        ))->addElement('text', 'cc_num', array(
            'label'        => 'Card Number',
            'required'     => false,
            'allowEmpty'   => false,
            'class'        => 'text',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Enter your credit card number above.'
                )),
                array(new Pet_Validate_CCNum)
            )
        ));
        $month_opts = array(
            ''   => 'Month...',
            '01' => '01 - January',
            '02' => '02 - February',
            '03' => '03 - March',
            '04' => '04 - April',
            '05' => '05 - May',
            '06' => '06 - June',
            '07' => '07 - July',
            '08' => '08 - August',
            '09' => '09 - September',
            '10' => '10 - October',
            '11' => '11 - November',
            '12' => '12 - December'
        );
        $this->addElement('select', 'cc_exp_month', array(
            'multiOptions' => $month_opts, 
            'label'        => 'Expiration Month',
            'required'     => false,
            'allowEmpty'   => false,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter expiration month and year for the card.'
                )),
                array(new Pet_Validate_CCExpDate(array(
                    'month' => 'cc_exp_month',
                    'year'  => 'cc_exp_year'
                )))
            ),
            'registerInArrayValidator' => false
        ));
        $year_opts = array();
        $year = date('Y');
        for ($y = $year; $y < $year + 12; $y++) {
            $year_opts[$y] = $y;
        }
        $year_opts = array('' => 'Year...') + $year_opts;
        $this->addElement('select', 'cc_exp_year', array(
            'multiOptions' => $year_opts, 
            'label'        => 'Expiration Month',
            'required'     => false,
            'allowEmpty'   => false,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter expiration month and year for the card.'
                )),
                array(new Pet_Validate_CCExpDate(array(
                    'month' => 'cc_exp_month',
                    'year'  => 'cc_exp_year'
                )))
            ),
            'registerInArrayValidator' => false
        ))->addElement('text', 'cc_cvv', array(
            'label'        => 'Security Code',
            'required'     => false,
            'allowEmpty'   => false,
            'class'        => 'text',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please enter the security code from your card.'
                )),
                array('StringLength', true, array(
                    'max' => 4,
                    'messages' => 'The security code must be %max% characters or less.'
                )),
                array('Digits', true, array(
                    'messages' => 'The security code must contain only numbers.'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }
    
    public function isValid($data) {
        $payment_method = (isset($data['payment_method']) ?
            $data['payment_method'] : null);
        if ($payment_method == 'paypal') {
            $this->getElement('cc_num')->clearValidators();
            $this->getElement('cc_exp_month')->clearValidators();
            $this->getElement('cc_exp_year')->clearValidators();
            $this->getElement('cc_cvv')->clearValidators();
        }
        return parent::isValid($data);
    }

}
