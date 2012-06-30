<?php
/**
 * Admin product form
 * 
 */
class Form_Admin_Product extends Pet_Form {
    
    /**
     * @var Model_Product
     * 
     */
    protected $_product;

    /**
     * @var array
     * 
     */
    protected $_product_types;

    /**
     * @var array
     * 
     */
    protected $_download_formats;

    /**
     * @param Model_Product $product
     * @return void
     */
    public function setProduct($product) {
        $this->_product = $product;
    }

    /**
     * @param array
     * @return void
     */
    public function setProductTypes(array $product_types) {
        $this->_product_types = $product_types;
    }

    /**
     * @param array
     * @return void
     */
    public function setDownloadFormats(array $formats) {
        $this->_download_formats = $formats;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $prod_types = array('' => 'All products');
        foreach ($this->_product_types as $product_type) {
            $prod_types[$product_type->id] = $product_type->name;
        }
        $this->addElement('select', 'product_type', array(
            'label'        => 'Product Type',
            'required'     => false,
            'multiOptions' => $prod_types,
            'value'        => $this->_product->product_type_id
        ))->addElement('text', 'name', array(
            'label'     => 'Name',
            'required'  => true,
            'value'     => $this->_product->name
        ))->addElement('text', 'sku', array(
            'label'     => 'SKU',
            'required'  => true,
            'value'     => $this->_product->sku
        ))->addElement('text', 'cost', array(
            'label'     => 'Cost',
            'required'  => true,
            'value'     => $this->_product->cost
        ))->addElement('checkbox', 'active', array(
            'label'     => 'Active',
            'required'  => false,
            'class'     => 'checkbox',
            'value'     => $this->_product->active
        ))->addElement('checkbox', 'is_giftable', array(
            'label'     => 'Giftable?',
            'required'  => false,
            'class'     => 'checkbox',
            'value'     => $this->_product->is_giftable
        ));
        switch ($this->_product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                $this->addSubform(new Form_Admin_SubForm_Subscription(array(
                    'product'         => $this->_product
                )), 'subscription'); 
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                
                break;
            case Model_ProductType::DOWNLOAD:
                $this->addSubform(new Form_Admin_SubForm_Download(array(
                    'downloadFormats' => $this->_download_formats,
                    'product'         => $this->_product
                )), 'download'); 
                $this->download->populate($this->_product->toArray());
                break;
            case Model_ProductType::PHYSICAL:
                
                break;
            case Model_ProductType::COURSE:
                
                break;
        }
    }

}
