<?php
/**
 * Promo form
 * 
 */
class Form_Admin_Promo extends Pet_Form {
    
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
        ))->addElement('text', 'public_description', array(
            'label'        => 'Public Description',
            'required'     => false,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Public description is required'
                ))
            )
        ))->addElement('file', 'banner', array(
            'label'        => 'Banner',
            'required'     => false,
            'destination'  => '/private/tmp',
            'validators'   => array(
                array('Count', false, 1),
                array('Size', false, 10000000),
                array('Extension', false, 'jpg,png,gif')
            )
        ));
    }
    
}
