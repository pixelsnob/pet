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
     * @var array
     * 
     */
    protected $_product_types;

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
     * @param array
     * @return void
     */
    public function setProductTypes(array $product_types) {
        $this->_product_types = $product_types;
    }

    /**
     * @param string
     * @return void
     */
    public function setMode($mode) {
        $this->_mode = $mode;
    }

    /**
     * @param array
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
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setName('product_edit');
        $prod_types = array('' => 'Please select...');
        foreach ($this->_product_types as $product_type) {
            $prod_types[$product_type->id] = $product_type->name;
        }
        // Elements common to all product types
        $this->addElement('select', 'product_type_id', array(
            'label'        => 'Product Type',
            'required'     => true,
            'multiOptions' => $prod_types,
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

}
