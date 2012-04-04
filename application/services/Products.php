<?php
/** 
 * @package Service_Products
 * 
 */
class Service_Products {
    
    /**
     * @param string
     * 
     */
    protected $_message = '';

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_products = new Model_Mapper_Products;
    }
    
    public function getById($product_id) {
        return $this->_products->getById($product_id);
    }

    public function getSubscriptionsByZoneId($zone_id, $is_renewal = null) {
        return $this->_products->getSubscriptionsByZoneId($zone_id,
            $is_renewal);
    }

    public function getSubscriptionTermSelectForm(array $subscriptions) {
        $form = new Default_Form_SubscriptionTermSelect;
        $subs = array();
        foreach ($subscriptions as $sub) {
            $subs[$sub->product_id] = $sub->name;
        }
        $form->product_id->setMultiOptions($subs);
        return $form; 
    }
}
