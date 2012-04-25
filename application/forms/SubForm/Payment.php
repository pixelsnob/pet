<?php
/**
 * Payment info subform
 * 
 */
class Form_SubForm_Payment extends Zend_Form_SubForm {
    
    /** 
     * @return void
     * 
     */
    public function init() {
        $payment_opts = array(
            'credit_card' => 'Credit Card',
            'paypal'      => 'Paypal Express Checkout'
        );
        $this->addElement('radio', 'payment_method', array(
            'multiOptions' => $payment_opts, 
            'label'        => 'Payment Method',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Payment method is required'
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
                array(new Pet_Validate_CCNum)
                //    $cart->payment->payment_method == 'credit_card'), true)
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
                array('Callback', true, array(
                    'callback' => array($this, 'isRequiredForPaypal'),
                    'messages' => 'Expiration month is required'
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
                array('Callback', true, array(
                    'callback' => array($this, 'isRequiredForPaypal'),
                    'messages' => 'Expiration year is required'
                ))
            ),
            'registerInArrayValidator' => false
        ))->addElement('text', 'cc_cvv', array(
            'label'        => 'Security Code',
            'required'     => false,
            'allowEmpty'   => false,
            'class'        => 'text',
            'validators'   => array(
                array('Callback', true, array(
                    'callback' => array($this, 'isRequiredForPaypal'),
                    'messages' => 'Security code is required'
                )),
                array('StringLength', true, array(
                    'max' => 4,
                    'messages' => 'Security code must be %max% characters or less'
                ))
            )
        ))->setElementFilters(array('StringTrim'));
    }
    
    /**
     * @param string $value
     * @param array $context
     * 
     */
    public function isRequiredForPaypal($value, $context) {
        $payment_method = (isset($context['payment_method']) ?
            $context['payment_method'] : '');
        if ($payment_method == 'paypal') {
            return true;
        }
        return ((bool) strlen(trim($value)));
    }
}
