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
    
    /**
     * @return Model_Product_Abstract
     * 
     */
    public function getById($product_id) {
        return $this->_products->getById($product_id);
    }

    /**
     * @param int $zone_id
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return array
     * 
     */
    public function getSubscriptionsByZoneId($zone_id, $is_giftable = null,
                                             $is_renewal = false) {
        return $this->_products->getSubscriptionsByZoneId($zone_id,
            $is_giftable, $is_renewal);
    }

    /**
     * @param array $subscriptions
     * @param int $zone_id
     * @param bool $is_gift
     * @param bool $is_renewal
     * @return Form_SubscriptionTermSelect
     * 
     */
    public function getSubscriptionTermSelectForm(array $subscriptions,
                                                  $zone_id, $is_gift,
                                                  $is_renewal) {
        $form = new Form_SubscriptionTermSelect(array(
            'zoneId'  => $zone_id,
            'isGift'    => $is_gift,
            'isRenewal' => $is_renewal
        ));
        $subs = array();
        foreach ($subscriptions as $sub) {
            $subs[$sub->product_id] = $sub->name . ($is_gift ? ' (gift)' : '');
        }
        $form->product_id->setMultiOptions($subs);
        return $form; 
    }

    /**
     * @param mixed $is_giftable
     * @param bool $is_renewal
     * @return array
     * 
     */
    public function getDigitalSubscriptions($is_giftable = null,
                                            $is_renewal = false) {
        return $this->_products->getDigitalSubscriptions($is_giftable, $is_renewal);
    }

    /**
     * @param array $subscriptions
     * @param bool $is_gift
     * @param bool $is_renewal
     * @return Form_DigitalSubscriptionSelect 
     * 
     */
    public function getDigitalSubscriptionSelectForm(array $subscriptions,
                                                     $is_gift = false,
                                                     $is_renewal = false) {
        $form = new Form_DigitalSubscriptionSelect(array(
            'isGift'    => $is_gift,
            'isRenewal' => $is_renewal
        ));
        $subs = array();
        foreach ($subscriptions as $sub) {
            $subs[$sub->product_id] = $sub->name . ($is_gift ? ' (gift)' : '');
        }
        $form->product_id->setMultiOptions($subs);
        return $form; 
    }

    /**
     * @param string $country_name
     * @return Model_SubscriptionZone 
     * 
     */
    public function getSubscriptionZoneByName($country_name) {
        $sz_mapper = new Model_Mapper_SubscriptionZones;
        return $sz_mapper->getByName($country_name);
    }
    
    /**
     * @return Zend_Db_Table_Rowset object 
     * 
     */
    public function getPhysicalProducts() {
        return $this->_products->getPhysicalProducts();
    }
}
