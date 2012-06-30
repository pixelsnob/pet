<?php
/**
 * Admin physical product subform
 * 
 */
class Form_Admin_SubForm_Physical extends Zend_Form_SubForm {
    
    /**
     * @var Model_Product
     * 
     */
    protected $_product;

    /**
     * @param Model_Product $product
     * @return void
     */
    public function setProduct($product) {
        $this->_product = $product;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false,
            'value'        => $this->_product->description
        ));

    }

}
