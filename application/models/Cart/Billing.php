<?php
/**
 * @package Model_Cart_Billing
 * 
 */
class Model_Cart_Billing extends Pet_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'bill_first_nm'       => '',
        'bill_last_nm'        => '',
        'bill_company_nm'     => '',
        'bill_addr1'          => '',
        'bill_addr2'          => '',
        'bill_city'           => '',
        'bill_state'          => '',
        'bill_zip'            => '',
        'bill_country'        => '',
        'email'               => '',
        'confirm_email'       => '',
        'bill_phone'          => '',
        'bill_to_ship'        => '1'
    );
}
