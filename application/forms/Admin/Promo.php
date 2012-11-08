<?php
/**
 * Promo form
 * 
 */
class Form_Admin_Promo extends Pet_Form {

    /** 
     * @var Model_Promo
     * 
     */
    protected $_promo;

    
    /** 
     * @var Model_Mapper_Promos
     * 
     */
    protected $_promos_mapper;

    /**
     * @var array
     * 
     */
    protected $_products = array();

    /**
     * @param Model_Promo $promo
     * @return void
     * 
     */
    public function setPromo(Model_Promo $promo) {
        $this->_promo = $promo;
    }

    /**
     * @param Model_Mapper_Promos $mapper
     * @return void
     * 
     */
    public function setPromosMapper(Model_Mapper_Promos $mapper) {
        $this->_promos_mapper = $mapper;
    }

    /**
     * @param array $products
     * @return void
     * 
     */
    public function setProducts(array $products) {
        $this->_products = $products;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART)->setName('promo_edit');
        $this->addElement('text', 'code', array(
            'label'        => 'Promo Code',
            'required'     => true,
            'title'        => 'Look at the format of other similar promos and mimic that.',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Promo code is required'
                )),
                array('Callback', true, array(
                    'callback' => array($this, 'promoExists'),
                    'messages' => 'Promo code already exists'
                ))
            )
        ))->addElement('text', 'expiration', array(
            'label'        => 'Expiration',
            'required'     => true,
            'title'        => '3 months is a good benchmark for a 1-time promo, go till year end if it is one you will use repeatedly.',
            'class' => 'datepicker datepicker-no-max',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Expiration is required'
                )),
                array('Date', true, array(
                    'messages' => 'Invalid date'
                ))
            )
        ))->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'title'        => '150 characters or less, please.',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Description is required'
                ))
            )
        ))->addElement('textarea', 'public_description', array(
            'label'        => 'Public Description',
            'title'        => 'Appears on the checkout page under the Buy Now button. It should describe what the customer will receive, and how soon. Go for 250 characters or less.',

            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Public description is required'
                ))
            )
        ))->addElement('textarea', 'receipt_description', array(
            'label'        => 'Receipt Description',
            'title'        => 'This field will show up on the customer receipt. It should remind them exactly what they will get with the promo, and set the expectation for delivery. Try to keep it at 400 characters or less.',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Receipt description is required'
                ))
            )
        ))->addElement('text', 'discount', array(
            'label'        => 'Discount',
            'required'     => false,
            'title'        => 'How many dollars off the regular price?',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Discount is required'
                )),
                array(new Pet_Validate_Currency, true),
                array('LessThan', true, array(
                    'max' => 1000,
                    'messages' => 'Amount must be less than $%max%'
                ))
            )
        ))->addElement('file', 'banner', array(
            'label'        => 'Banner',
            'required'     => false,
            'title'        => 'Image size should be 550 px wide by 350 px high, save as a .png.',
            'validators'   => array(
                array('Count', false, 1),
                array('Size', false, array(
                    'max'     => 200000,
                    'messages' => 'File size must be less than 200K'
                )),
                array('Extension', false, array(
                    'extensions' => 'jpg,gif,png',
                    'messages'   => 'Only .jpg, .gif, and .png are allowed'
                ))
            )
        ))->addElement('text', 'extra_days', array(
            'label'        => 'Extra Days',
            'required'     => false,
            'title'        => 'How many extra months -- assuming 31 day months in days -- does this offer. Example- if the promo is Get An Extra Month Free, then this value should be 31. For two months, use 62, etc.',
            'validators'   => array(
                array('Digits', true, array(
                    'messages' => 'Extra days must be a whole, positive number'
                ))
            )
        ))->addElement('multiselect', 'products', array(
            'label'        => 'Applicable Products',
            'class'        => 'multi',
            'multiOptions' => $this->_products,
            'required'     => false,
            'title'        => 'To which product does this promo apply? If a subscription promotion, then look for sub type (All Access or Digital) and include the ones you need. If a standalone product (DVD or course), choose from those. Shift-click to select multiple in a row, or Command-click to select non-sequential products.',
            'validators'   => array(
            )
        ))->addElement('hidden', 'tmp_banner')
          ->addElement('hidden', 'delete_banner')
          ->setElementFilters(array('StringTrim'));
        // Banner upload
        $adapter = $this->banner->getTransferAdapter();
        $uid = md5(uniqid(mt_rand(), true));
        $filename = $adapter->getFileName();
        if ($filename) {
            $rename_filter = new Zend_Filter_File_Rename(array(
                'target'    => "/tmp/{$uid}",
                'overwrite' => false
            ));
            $adapter->addFilter($rename_filter);
        }
    }
    
    /**
     * @param string $value
     * @return bool
     * 
     */
    public function promoExists($value) {
        $promo = $this->_promos_mapper->getByCode($value, false);
        if (!$promo || ($promo && $this->_promo && $promo->id ==
                $this->_promo->id)) {
            return true;
        }
        return false;
    }
}
