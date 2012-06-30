<?php
/**
 * Admin subscription subform
 * 
 */
class Form_Admin_SubForm_Subscription extends Form_SubForm {
    
    /**
     * @var Model_Product
     * 
     */
    protected $_product;

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
     * @param array $download_formats
     * @return void
     */
    public function setDownloadFormats($download_formats) {
        $this->_download_formats = $download_formats;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $df = array('' => 'Please select...');
        foreach ($this->_download_formats as $format) {
            $df[$format->id] = $format->name;
        }
        $this->addElement('select', 'download_format', array(
            'label'        => 'Download Format',
            'required'     => false,
            'multiOptions' => $df,
            'value'        => $this->_product->download_format
        ))->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => false
        ))->addElement('text', 'date', array(
            'label'        => 'Download Format',
            'required'     => false,
        ))->addElement('text', 'path', array(
            'label'        => 'Download Format',
            'required'     => false,
        ))->addElement('text', 'size', array(
            'label'        => 'Download Format',
            'required'     => false,
        ))->addElement('text', 'date', array(
            'label'        => 'Download Format',
            'required'     => false,
        ))->addElement('checkbox', 'subscriber_only', array(
            'label'        => 'Subscriber Only',
            'required'     => false
        ));
    }

}
