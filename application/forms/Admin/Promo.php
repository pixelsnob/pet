<?php
/**
 * Promo form
 * 
 */
class Form_Admin_Promo extends Pet_Form {
    
    /** 
     * @var Model_Mapper_Promos
     * 
     */
    protected $_promos_mapper;

    /**
     * @param Model_Mapper_Promos $mapper
     * @return void
     * 
     */
    public function setPromosMapper(Model_Mapper_Promos $mapper) {
        $this->_promos_mapper = $mapper;
    }

    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        // Elements common to all product types
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART)->setName('promo_edit');
        $this->addElement('text', 'code', array(
            'label'        => 'Promo Code',
            'required'     => true,
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
            'class' => 'datepicker datepicker-min-today',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Expiration is required'
                )),
                array('Date', true, array(
                    'messages' => 'Invalid date'
                )),
                array(new Pet_Validate_DateNotBeforeToday, true)
            )
        ))->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Description is required'
                ))
            )
        ))->addElement('textarea', 'public_description', array(
            'label'        => 'Public Description',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Public description is required'
                ))
            )
        ))->addElement('textarea', 'receipt_description', array(
            'label'        => 'Receipt Description',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Receipt description is required'
                ))
            )
        ))->addElement('text', 'discount', array(
            'label'        => 'Discount',
            'required'     => false,
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
            'destination'  => '/private/tmp',
            'validators'   => array(
                array('Count', false, 1),
                array('Size', false, 10000000),
                array('Extension', false, array(
                    'extensions' => 'jpg,gif,png',
                    'messages'   => 'Only .jpg, .gif, and .png are allowed'
                ))
            )
        ))->addElement('text', 'extra_days', array(
            'label'        => 'Extra Days',
            'required'     => false,
            'validators'   => array(
                array('Digits', true, array(
                    'messages' => 'Extra days must be a whole, positive number'
                ))
            )
        ))->addElement('hidden', 'tmp_banner')
          ->setElementFilters(array('StringTrim'));
        // Banner upload
        $adapter = $this->banner->getTransferAdapter();
        $uid = md5(uniqid(mt_rand(), true));
        $filename = $adapter->getFileName();
        if ($filename) {
            $filename = basename($filename);
            $rename_filter = new Zend_Filter_File_Rename(array(
                'target'    => "/tmp/{$uid}{$filename}",
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
        return !$promo;
    }
}
