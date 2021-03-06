<?php
/**
 * @package Model_Order
 * 
 */
class Model_Order extends Pet_Model_Abstract {

    public $_data = array(
        'id' => null,
        'user_id' => null,
        'promo_id' => null,
        'date_created' => null,
        'date_updated' => null,
        'email' => null,
        'billing_first_name' => null,
        'billing_last_name' => null,
        'billing_address' => null,
        'billing_address_2' => null,
        'billing_company' => null,
        'billing_city' => null,
        'billing_country' => null,
        'billing_state' => null,
        'billing_postal_code' => null,
        'billing_phone' => null,
        'shipping_first_name' => null,
        'shipping_last_name' => null,
        'shipping_address' => null,
        'shipping_address_2' => null,
        'shipping_company' => null,
        'shipping_city' => null,
        'shipping_state' => null,
        'shipping_postal_code' => null,
        'shipping_country' => null,
        'shipping_phone' => null,
        'shipping' => 0,
        'discount' => 0,
        'total' => 0,
        'phone_order' => 0,
        'active' => 1,

        'user' => null,
        'user_profile' => null,
        'payments' => array(),
        'products' => array(),
        'subscriptions' => array(),
        'gifts' => array(),
        'promo' => null
    );
    
    /** 
     * @param bool Whether to include references to other objects
     * @return array
     * 
     */
    public function toArray($refs = false) {
        $data = $this->_data;
        if (!$refs) {
            unset($data['user']);
            unset($data['user_profile']);
            unset($data['products']);
            unset($data['payments']);
            unset($data['subscriptions']);
            unset($data['gifts']);
            unset($data['promo']);
        } else {
            $data['user'] = $data['user']->toArray();
            $data['user_profile'] = $data['user_profile']->toArray();
            $products = array();
            foreach ($data['products'] as $product) {
                $products[] = $product->toArray();
            }
            $data['products'] = $products;
            $payments = array();
            foreach ($data['payments'] as $payment) {
                $payments[] = $payment->toArray(true);
            }
            $data['payments'] = $payments;
            $subscriptions = array();
            foreach ($data['subscriptions'] as $subscription) {
                $subscriptions[] = $subscription->toArray();
            }
            $data['subscriptions'] = $subscriptions;
            $gifts = array();
            foreach ($data['gifts'] as $gift) {
                $gifts[] = $gift->toArray();
            }
            $data['gifts'] = $gifts;
            if ($data['promo']) {
                $data['promo'] = $data['promo']->toArray();
            }
        }
        return $data;
    }
}

