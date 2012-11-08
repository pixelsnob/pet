<?php
/**
 * Cart form
 * 
 */
class Form_Cart extends Pet_Form {
    
    /**
     * @var Model_Cart
     * 
     */
    protected $_cart;
    
    /** 
     * @param Model_Cart $cart
     * @return void
     * 
     */
    public function setCart($cart) {
        $this->_cart = $cart;
    }
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $qty = new Zend_Form_SubForm;
        $this->addSubForm($qty, 'qty');
        $no_qty_types = array(
            Model_ProductType::SUBSCRIPTION,
            Model_ProductType::DIGITAL_SUBSCRIPTION
        );
        foreach ($this->_cart->products as $product) {
            $qty->addElement('text', $product->key, array(
                'label' => 'Quantity:',
                'belongsTo' => 'qty',
                'required' => true,
                'validators'   => array(
                    array('NotEmpty', true, array(
                        'messages' => 'Please enter a quantity'
                    )),
                    // Value must be a number and 0 or greater
                    array('Callback', true, array('callback' => function($v) {
                        return (preg_match('/\D/', $v) == 0 && $v > 0);
                    }, 'messages' => 'Quantity is not valid')),
                    array('StringLength', true, array(
                        'max' => 1,
                        'messages' => 'Quantity is not valid'
                    ))
                ),
                'decorators' => array(
                    'ViewHelper',
                    'Label',
                    'Errors'
                )
            ));
            if (!$product->isGift() && in_array($product->product_type_id, $no_qty_types)) {
                $qty->getElement($product->key)
                    ->setAttrib('readonly', true)
                    ->setOptions(array('class' => 'readonly'))
                    ->addValidator('LessThan', true, array(
                        'max' => 2,
                        'messages' => 'Multiple quantities of this item not allowed'
                    ));
            } else {
                $qty->getElement($product->key)
                    ->addValidator('LessThan', true, array(
                        'max' => 10,
                        'messages' => 'Maximum quantity exceeded'
                    ));
            }
        }
    }
}
