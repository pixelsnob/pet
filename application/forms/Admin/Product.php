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
     * @var array
     * 
     */
    protected $_subscription_zones;

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
        $prod_types = array('' => 'Please select...');
        foreach ($this->_product_types as $product_type) {
            $prod_types[$product_type->id] = $product_type->name;
        }
        // Elements common to all product types
        $this->addElement('select', 'product_type', array(
            'label'        => 'Product Type',
            'required'     => true,
            'multiOptions' => $prod_types,
            'value'        => $this->_product->product_type_id,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Product type is required'
                ))
            )
        ))->addElement('text', 'name', array(
            'label'     => 'Name',
            'required'  => true,
            'value'     => $this->_product->name,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Name is required'
                ))
            )
        ))->addElement('text', 'sku', array(
            'label'     => 'SKU',
            'required'  => true,
            'value'     => $this->_product->sku,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'SKU is required'
                ))
            )
        ))->addElement('text', 'cost', array(
            'label'     => 'Cost',
            'required'  => true,
            'value'     => $this->_product->cost,
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
            'value'     => $this->_product->active
        ))->addElement('checkbox', 'is_giftable', array(
            'label'     => 'Giftable?',
            'required'  => false,
            'class'     => 'checkbox',
            'value'     => $this->_product->is_giftable
        ));
        // Product type forms
        switch ($this->_product->product_type_id) {
            case Model_ProductType::SUBSCRIPTION:
                $this->addSubform(new Form_Admin_SubForm_Subscription(array(
                    'subscriptionZones' => $this->_subscription_zones
                )), 'subscription'); 
                $this->subscription->populate($this->_product->toArray());
                break;
            case Model_ProductType::DIGITAL_SUBSCRIPTION:
                $this->addSubform(new Form_Admin_SubForm_DigitalSubscription,
                    'digital'); 
                $this->digital->populate($this->_product->toArray());
                break;
            case Model_ProductType::DOWNLOAD:
                $this->addSubform(new Form_Admin_SubForm_Download(array(
                    'downloadFormats' => $this->_download_formats,
                )), 'download'); 
                $this->download->populate($this->_product->toArray());
                break;
            case Model_ProductType::PHYSICAL:
                $this->addSubform(new Form_Admin_SubForm_Physical, 'physical'); 
                $this->physical->populate($this->_product->toArray());
                break;
            case Model_ProductType::COURSE:
                $this->addSubform(new Form_Admin_SubForm_Course, 'course'); 
                $this->course->populate($this->_product->toArray());
                break;
        }
    }

}
