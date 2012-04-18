<?php
/**
 * Payment info subform
 * 
 * @package Form_Cart_SubForm_Payment
 * 
 */
class Form_Cart_SubForm_Payment extends Zend_Form_SubForm {
    
    /**
     * @var Model_Mapper_Cart
     */
    private $_cart_mapper;

    /**
     * Configures form
     * 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setElementFilters(array('StringTrim'));
    }
    
    /** 
     * @return void
     * 
     */
    private function init() {
        $payment_opts = array(
            'credit_card' => 'Credit Card',
            'paypal'      => 'Paypal Express Checkout'
        );
        /*$this->addElement('radio', 'payment_method', array(
            'multiOptions' => $payment_opts, 
            'label'        => 'Payment Method',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Payment method is required'
                ))
            ),
            'viewScript' => 'decorators/radio_list.phtml',
            'decorators'   => array(
                'ViewScript',
                'Errors'
            ),
            'disableLoadDefaultDecorators' => true,
            'registerInArrayValidator' => false,
        ))->addElement('text', 'cc_num', array(
            'value'        => $cart->payment->cc_num,
            'label'        => 'Card Number',
            'required'     => false,
            'allowEmpty'   => false,
            'class'        => 'text',
            'validators'   => array(
                array(new Onone_Validate_CCNum(
                    $cart->payment->payment_method == 'credit_card'), true)
            )
        ));
        $month_opts = array(
            ''   => 'Please select month...',
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
            'value'        => $cart->payment->cc_exp_month,
            'multiOptions' => $month_opts, 
            'label'        => 'Expiration Month',
            'required'     => false,
            'allowEmpty'   => false,
            'validators'   => array(
                array('Callback', true, array(
                    'callback' => array($this, 'isRequiredForPaypal'),
                    'messages' => 'Expiration month is required'
                )),
                array(new Onone_Validate_CCExpDate(array(
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
        $year_opts = array('' => 'Please select year...') + $year_opts;
        $this->addElement('select', 'cc_exp_year', array(
            'value'        => $cart->payment->cc_exp_year,
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
            'value'        => $cart->payment->cc_cvv,
            'label'        => 'Security Code',
            'required'     => false,
            'allowEmpty'   => false,
            'class'        => 'text',
            'validators'   => array(
                array('Callback', true, array(
                    'callback' => array($this, 'isRequiredForPaypal'),
                    'messages' => 'Security code is required'
                ))
            )
        ));*/
    }
    
    public function isRequiredForPaypal($value) {
        $cart = $this->_cart_mapper->get();
        if ($cart->payment->payment_method == 'paypal') {
            return true;
        }
        return (bool) $value;
    }
}
