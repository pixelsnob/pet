<?php
/**
 * Cart form
 * 
 */
class Default_Form_Cart extends Pet_Form {
    
    protected $_cart = array();
    
    public function setCart($cart) {
        $this->_cart = $cart;
    }
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('cart');
        $qty = new Zend_Form_Subform;
        $this->addSubForm($qty, 'qty');
        $no_qty_types = array(
            Model_ProductType::SUBSCRIPTION,
            Model_ProductType::DIGITAL_SUBSCRIPTION
        );
        foreach ($this->_cart->products as $product) {
            $readonly = null;
            $qty->addElement('text', $product->product_id, array(
                'label' => 'Quantity',
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
                )
            ));
            if (in_array($product->product_type_id, $no_qty_types)) {
                $qty->getElement($product->product_id)
                    ->setAttrib('readonly', true)
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
        }
    }
}
