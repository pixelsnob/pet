<?php
/**
 * Credit payment form
 * 
 */
class Form_Admin_CreditPayment extends Pet_Form {
    
    /**
     * @var int
     * 
     */
    protected $_orig_amount;

    /**
     * @var int
     * 
     */
    protected $_order_payment_id;

    /**
     * @param int
     * @return void
     */
    public function setOrigAmount($orig_amount) {
        $this->_orig_amount = $orig_amount;
    }

    /**
     * @param int
     * @return void
     */
    public function setOrderPaymentId($id) {
        $this->_order_payment_id = $id;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $orig_amount = $this->_orig_amount;
        $this->setName('credit_payment');
        $this->addElement('text', 'amount', array(
            'label'        => 'Credit Amount',
            'required'     => false,
            'value'        => $this->_orig_amount,
            'validators'   => array(
                array(new Pet_Validate_Currency, true),
                array('Callback', true, array(
                    'callback' => function($value) use ($orig_amount) {
                        return ($value <= $orig_amount);
                    },
                    'messages' => "Amount must be less than \$$orig_amount"
                ))
            ),
            'registerInArrayValidator' => false
        ))->addElement('hidden', 'order_payment_id', array(
            'value' => $this->_order_payment_id
        ));
    }


}
