<?php
/**
 * Promo code subform
 * 
 */
class Form_SubForm_Promo extends Zend_Form_SubForm {
    
    /**
     * @var Model_Cart
     * 
     */
    protected $_cart;

    /**
     * @var Pet_Model_Mapper_Abstract 
     * 
     */
    protected $_mapper;

    /**
     * @param Pet_Model_Mapper_Abstract $mapper
     * @return void
     */
    public function setMapper(Pet_Model_Mapper_Abstract $mapper) {
        $this->_mapper = $mapper;
    }
    
    /** 
     * @param Model_Cart $cart
     * @return void
     * 
     */
    public function setCart(Model_Cart $cart) {
        $this->_cart = $cart;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        $this->addElement('text', 'promo_code', array(
            'label'        => 'Promo Code',
            'required'     => false,
            'validators'   => array(
                array('Callback', true, array(
                    'callback' => array($this, 'isPromoValid'),
                    'messages' => 'Promo is not valid'
                ))
            )
        ));
    }
    
    /**
     * @param string $value
     * @return bool
     * 
     */
    public function isPromoValid($value) {
        $promo = $this->_mapper->getUnexpiredPromoByCode($value);
        if (!$promo) {
            return false;
        }
        return $this->_cart->getValidator()->validatePromo($promo);
    }
    
}