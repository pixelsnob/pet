<?php
/**
 * Admin course subform
 * 
 */
class Form_Admin_SubForm_Course extends Zend_Form_SubForm {
    
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
        ))->addElement('text', 'slug', array(
            'label'        => 'Slug',
            'required'     => false,
            'value'        => $this->_product->slug
        ))->addElement('checkbox', 'free', array(
            'label'        => 'Free?',
            'class'        => 'checkbox',
            'required'     => false,
            'value'        => $this->_product->description
        ));
    }

}
