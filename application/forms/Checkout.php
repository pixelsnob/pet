<?php
/**
 * Checkout form
 * 
 */
class Form_Checkout extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        /*$qty = new Zend_Form_SubForm;
        $this->addSubForm($qty, 'qty');
        $no_qty_types = array(
            Model_ProductType::SUBSCRIPTION,
            Model_ProductType::DIGITAL_SUBSCRIPTION
        );
        foreach ($this->_cart->products as $product) {
            $qty->addElement('text', $product->product_id, array(
                'label' => 'Quantity:',
                'belongsTo' => 'qty',
                'required' => true,
                'validators'   => array(
                    array('NotEmpty', true, array(
                        'messages' => 'Please enter a quantity'
                    )),
                    // Value must be a number and 0 or greater
                    array('Callback', true, array('callback' => function($v) {
                        return (preg_match('/\D/', $v) == 0 && $v >= 0);
                    }, 'messages' => 'Quantity is not valid'))
                ),
                'decorators' => array(
                    'ViewHelper',
                    'Label',
                    'Errors'
                )
            ));
            if (!$product->is_gift && in_array($product->product_type_id, $no_qty_types)) {
                $qty->getElement($product->product_id)
                    ->setAttrib('readonly', true)
                    ->setOptions(array('class' => 'readonly'))
                    ->addValidator('LessThan', true, array(
                        'max' => 2,
                        'messages' => 'Multiple quantities of this item not allowed'
                    ));
            } else {
                $qty->getElement($product->product_id)
                    ->addValidator('LessThan', true, array(
                        'max' => 10,
                        'messages' => 'Maximum quantity exceeded'
                    ));
            }
        }*/
    }
}
