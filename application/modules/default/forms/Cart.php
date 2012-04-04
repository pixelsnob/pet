<?php
/**
 * Cart form
 * 
 */
class Default_Form_Cart extends Pet_Form {
    
    /**
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $this->setMethod('post')->setName('susbscription_term_select');
        /*$this->addElement('radio', 'product_id', array(
            'label' => 'Subscriptions',
            'required' => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Please select a term'
                ))
            )
        ));*/
        
    }
}
