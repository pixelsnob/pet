<?php
/** 
 * @package Service_Products
 * 
 */
class Service_Products extends Pet_Service {
    
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

    public function getSubscriptionsByZoneId($zone_id, $is_gift, $is_renewal = null) {
        return $this->_products->getSubscriptionsByZoneId($zone_id, $is_gift,
            $is_renewal);
    }

    public function getSubscriptionTermSelectForm(array $subscriptions,
                                                  $zone_id, $is_gift, $renewal) {
        $form = new Default_Form_SubscriptionTermSelect(array(
            'zoneId'  => $zone_id,
            'isGift'    => $is_gift,
            'renewal' => $renewal
        ));
        $subs = array();
        foreach ($subscriptions as $sub) {
            $subs[$sub->product_id] = $sub->name . ($is_gift ? ' (gift)' : '');
        }
        $form->product_id->setMultiOptions($subs);
        return $form; 
    }

    public function getDigitalSubscriptions() {
        return $this->_products->getDigitalSubscriptions();
    }

    public function getDigitalSubscriptionSelectForm(array $subscriptions) {
        $form = new Default_Form_DigitalSubscriptionSelect;
        $subs = array();
        foreach ($subscriptions as $sub) {
            $subs[$sub->product_id] = $sub->name;
        }
        $form->product_id->setMultiOptions($subs);
        return $form; 
    }

    public function getSubscriptionZoneByName($country_name) {
        $sz_mapper = new Model_Mapper_SubscriptionZones;
        return $sz_mapper->getByName($country_name);
    }
}
