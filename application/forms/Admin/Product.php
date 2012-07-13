<?php
/**
 * Admin product form
 * 
 */
class Form_Admin_Product extends Pet_Form {

    /**
     * @var string
     * 
     */
    protected $_mode;

    /**
     * @var Model_Product
     * 
     */
    protected $_product;
    
    /**
     * @var int
     * 
     */
    protected $_product_type_id;

    /**
     * @var array
     * 
     */
    protected $_download_formats;

    /**
     * @var array
     * 
     */
    protected $_subscription_zones;

    /**
     * @var Model_Mapper_Products
     * 
     */
    protected $_products_mapper;

    /**
     * @param string
     * @return void
     */
    public function setMode($mode) {
        $this->_mode = $mode;
    }

    /**
     * @param Model_Product_Abstract
     * @return void
     */
    public function setProduct(Model_Product_Abstract $product) {
        $this->_product = $product;
    }

    /**
     * @param int
     * @return void
     */
    public function setProductTypeId($id) {
        $this->_product_type_id = $id;
    }

    /**
     * @param array
     * @return void
     */
    public function setDownloadFormats(array $formats) {
        $this->_download_formats = $formats;
    }

    /**
     * @param array
     * @return void
     */
    public function setSubscriptionZones(array $zones) {
        $this->_subscription_zones = $zones;
    }

    /**
     * @param array
     * @return void
     */
    public function setProductsMapper(Model_Mapper_Products $products_mapper) {
        $this->_products_mapper = $products_mapper;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setName('product_edit');
        $product_types = $this->_products_mapper->getProductTypes();
        $prod_type_opts = array('' => 'Please select...');
        foreach ($product_types as $product_type) {
            $prod_type_opts[$product_type->id] = $product_type->name;
        }
        // Elements common to all product types
        $this->addElement('select', 'product_type_id', array(
            'label'        => 'Product Type',
            'required'     => true,
            'multiOptions' => $prod_type_opts,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Product type is required'
                ))
            )
        ))->addElement('text', 'name', array(
            'label'     => 'Name',
            'required'  => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Name is required'
                ))
            )
        ))->addElement('text', 'sku', array(
            'label'     => 'SKU',
            'required'  => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'SKU is required'
                )),
                array('Callback', true, array(
                    'callback' => array($this, 'validateSkuExists'),
                    'messages' => 'That SKU is already in use'
                ))
            )
        ))->addElement('text', 'cost', array(
            'label'     => 'Cost',
            'required'  => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Cost is required'
                )),
                array(new Pet_Validate_Currency, true)
            )
        ))->addElement('checkbox', 'active', array(
            'label'     => 'Active',
            'required'  => false,
            'class'     => 'checkbox',
            'checked'   => true
        ))->addElement('checkbox', 'is_giftable', array(
            'label'     => 'Giftable?',
            'required'  => false,
            'class'     => 'checkbox',
        ));
        if ($this->_mode == 'edit') {
            $this->product_type_id->setOptions(array('disabled' => true));
            $this->addElement('hidden', 'product_type_id_2', array(
                'name'  => 'product_type_id_2',
                'value' => $this->_product_type_id    
            ));
        }

        // Product type forms
        switch ($this->_product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                $this->addSubform(new Form_Admin_SubForm_Subscription(array(
                    'subscriptionZones' => $this->_subscription_zones
                )), 'subscription'); 
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                $this->addSubform(new Form_Admin_SubForm_DigitalSubscription,
                    'digital'); 
                break;
            case Model_ProductType::DOWNLOAD:
                $this->addSubform(new Form_Admin_SubForm_Download(array(
                    'downloadFormats' => $this->_download_formats,
                )), 'download'); 
                break;
            case Model_ProductType::PHYSICAL:
                $this->addSubform(new Form_Admin_SubForm_Physical, 'physical'); 
                break;
            case Model_ProductType::COURSE:
                $this->addSubform(new Form_Admin_SubForm_Course, 'course'); 
                break;
        }
    }
    
    /**
     * @param string $value
     * @return bool
     * 
     */
    public function validateSkuExists($value) {
        $product = $this->_products_mapper->getBySku($value);
        if (($this->_mode == 'edit' && $product && $product->sku == $value &&
                $product->product_id == $this->_product->product_id) ||
                (!$product || $product->sku != $value)) {
            return true;
        } elseif ($product) {
            return false;
        }
    }

}
