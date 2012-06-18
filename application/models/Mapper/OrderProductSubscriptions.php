<?php
/**
 * @package Model_Mapper_OrderProductSubscriptions
 * 
 */
class Model_Mapper_OrderProductSubscriptions extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_ops = new Model_DbTable_OrderProductSubscriptions;
    }
    
    /**
     * @param int $order_id
     * @return array
     * 
     */
    public function getByOrderId($order_id) {
        $subs = $this->_ops->getByOrderId($order_id);
        $products_mapper = new Model_Mapper_Products;
        $subs_array = array();
        if ($subs) {
            foreach ($subs as $sub) {
                $temp_sub = new Model_OrderProductSubscription(
                    $sub->toArray());
                $temp_product = $products_mapper->getById($sub->product_id);
                $temp_sub->product = $temp_product;
                $subs_array[] = $temp_sub;
            }
            return $subs_array;
        }
    }

    /**
     * @param int $user_id
     * @param mixed $digital_only
     * @return Zend_DbTable_Row_Abstract 
     * 
     */
    public function getUnexpiredByUserId($user_id, $digital_only = null) {
        $ops = $this->_ops->getUnexpiredByUserId($user_id, $digital_only); 
        if ($ops) {
            $ops_model = new Model_OrderProductSubscription($ops->toArray());
            return $ops_model;
        }
    }
    
    /**
     * @param DateTime $expiration
     * @return array An array of Model_OrderProductSubscription objects
     * 
     */
    public function getByExpiration(DateTime $expiration) {
        $products_mapper = new Model_Mapper_Products;
        $subs = $this->_ops->getByExpiration($expiration->format('Y-m-d'));
        $subs_array = array();
        if ($subs) {
            foreach ($subs as $sub) {
                $ops_model = new Model_OrderProductSubscription($sub->toArray());
                $ops_model->min_expiration = $sub['min_expiration'];
                if ($sub['product_id']) {
                    $product = $products_mapper->getById($sub['product_id']);
                    $ops_model->product = $product;
                }
                $subs_array[] = $ops_model;
            }
        }
        return $subs_array;
    }

    /**
     * @param string $region "usa", "intl" or null for all regions
     * @param Form_Admin_Report_MailingList $form
     * @return Zend_Db_Table_Rowset 
     */
    public function getMailingListReport($region = null,
                                         Form_Admin_Report_MailingList $form) {
        $start_date = new DateTime($form->date_range->start_date->getValue());
        $start_date->setTime(0, 0, 0);
        $mailing_list = $this->_ops->getMailingListReport(
            $region,
            $start_date->format('Y-m-d H:i:s')
        );
        return $mailing_list;
    }

    /**
     * @param string $start_date
     * @param int $opt_in
     * @param string $subscriber_type
     * @return Zend_Db_Table_Rowset 
     */
    public function getSubscribersReport(Form_Admin_Report_Subscribers $form) {
        $start_date = new DateTime($form->date_range->start_date->getValue());
        $start_date->setTime(0, 0, 0);
        $end_date = new DateTime($form->date_range->end_date->getValue());
        $end_date->setTime(23, 59, 59);
        $subscribers = $this->_ops->getSubscribersReport(array(
            'start_date'     => $start_date->format('Y-m-d H:i:s'),
            'end_date'       => $end_date->format('Y-m-d H:i:s'),
            'opt_in'         => $form->opt_in->getValue(),
            'opt_in_partner' => $form->opt_in_partner->getValue()
        ));
        return $subscribers;
    }

    /**
     * @param array $data
     * @return void
     * 
     */
    public function insert(array $data) {
        $ops_model = new Model_OrderProductSubscription($data);
        $this->_ops->insert($ops_model->toArray());
    }
    

}

