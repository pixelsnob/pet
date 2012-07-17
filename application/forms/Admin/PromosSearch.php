<?php
/**
 * Products search filter form
 * 
 */
class Form_Admin_ProductsSearch extends Pet_Form {
    
    /**
     * @var array
     * 
     */
    protected $_product_types;

    /**
     * @param array
     * @return void
     */
    public function setProductTypes(array $product_types) {
        $this->_product_types = $product_types;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('GET');
        $pt = array('' => 'All products');
        foreach ($this->_product_types as $product_type) {
            $pt[$product_type->id] = $product_type->name;
        }
        $this->addElement('select', 'product_type', array(
            'label'        => 'Product Type',
            'required'     => false,
            'multiOptions' => $pt
        ));
    }


}
