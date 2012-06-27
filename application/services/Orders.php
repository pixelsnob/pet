<?php
/**
 * Orders service layer
 *
 * @package Service_Orders
 * 
 */
class Service_Orders {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_orders = new Model_Mapper_Orders;
    }
    
    /**
     * @param int $id
     * @return null|Model_Order
     * 
     */
    public function getById($id) {
        return $this->_orders->get($id); 
    }
    
    /**
     * Convenience method used to pull an entire order
     * 
     * @param int $id
     * @return Model_Order
     * 
     */
    public function getFullOrder($id) {
        $op_mapper            = new Model_Mapper_OrderProducts;
        $ops_mapper           = new Model_Mapper_OrderProductSubscriptions;
        $opg_mapper           = new Model_Mapper_OrderProductGifts;
        $payments_mapper      = new Model_Mapper_OrderPayments;
        $products_mapper      = new Model_Mapper_Products;
        $users_svc            = new Service_Users;
        $profiles_mapper      = new Model_Mapper_UserProfiles;
        $promos_mapper        = new Model_Mapper_Promos;
        $msg_suffix           = " for order_id $id";
    
        $order = $this->getById($id);
        if (!$order) {
            $msg = 'Error retrieving order' . $msg_suffix;
            throw new Exception($msg);
        }
        $order->user          = $users_svc->getUser($order->user_id);
        $order->user_profile  = $users_svc->getProfile($order->user->id);
        $order->products      = $op_mapper->getByOrderId($order->id);
        $order->payments      = $payments_mapper->getByOrderId($order->id); 
        $order->subscriptions = $ops_mapper->getByOrderId($order->id);
        $order->expirations   = $users_svc->getExpirations($order->user->id);
        $order->gifts         = $opg_mapper->getByOrderId($order->id);
        if ($order->promo_id) {
            $order->promo     = $promos_mapper->getById($order->promo_id);
        }
        return $order;
    }
    
    /**
     * @return void
     * 
     */
    public function sendOrderEmails() {
        $db            = Zend_Db_Table::getDefaultAdapter();
        $op_mapper     = new Model_Mapper_OrderProducts;
        $logger        = Zend_Registry::get('log');
        $view          = Zend_Registry::get('view');
        try {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            $orders          = $this->_orders->getByEmailSent(false);
            $orders_sent     = array();
            $mail_exceptions = array();
            foreach ($orders as $order) {
                $full_order = $this->getFullOrder($order->id);
                if (!$full_order) {
                    $msg = "Error retrieving order for id {$order->id}";
                    throw new Exception($msg);
                }
                $view->order = $full_order;
                //print_r($full_order);
                $message = $view->render('emails/order.phtml');

                try {
                    $mail = new Zend_Mail;
                    $mail->setBodyText($view->render('emails/order.phtml'))
                         ->addTo($order->email)
                         ->setSubject('Photoshop Elements User Order: ' .
                                      $order->id)
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                    $orders_sent[] = $order->id;
                } catch (Exception $e2) {
                    $mail_exceptions[] = $e2;
                }
            }
            if (!empty($orders_sent)) {
                foreach ($orders_sent as $order_id) {
                    $this->_orders->updateEmailSent($order->id, true);
                }
            }
            $db->commit();
        } catch (Exception $e1) {
            print_r($e1);
            $db->rollback();
            $logger->log('Error updating database while sending order emails',
                Zend_Log::EMERG);
        }
        if (!empty($mail_exceptions)) {
            $mail_exceptions_str = implode(' ', $mail_exceptions);
            $logger->log("Error(s) sending order emails: " .
                $mail_exceptions_str, Zend_Log::EMERG);
        }
    }
    
    /**
     * This should be run once per day, preferably not too long after midnight...
     * 
     * @param DateTime $expiration The expiration date to use for the search
     * @return void
     * 
     */
    public function processRecurringBilling(DateTime $expiration) {
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway_mapper     = new Model_Mapper_PaymentGateway;
        $rb_logger          = new Model_Mapper_RecurringBillingTransactions;
        $view               = Zend_Registry::get('view');
        $logger             = Zend_Registry::get('log');
        $gateway_exceptions = array();
        $email_exceptions   = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $run_again          = false;
        $start_time         = time();
        try {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            $subs = $ops_mapper->getByExpiration($expiration);
            foreach ($subs as $sub) {
                if (!$sub->product || !$sub->product->is_recurring) {
                    continue;
                }
                $log_data = array(
                    'order'                     => null,
                    'gateway_calls'             => array(),
                    'exceptions'                => array()
                );
                $min_expiration = new DateTime($sub->min_expiration);
                // Stop repeating around a year -- reference transactions will only
                // last that long...
                if ($expiration->diff($min_expiration)->days > 335) {
                    // notify???????????????????????????
                    continue; 
                }
                // Get order
                $order = $this->getFullOrder($sub->order_id);
                if (!$order) {
                    throw new Exception('Error retrieving order');
                }
                $log_data['order'] = $order->toArray(true);
                if (!isset($order->payments[0])) {
                    throw new Exception('Error retrieving order payment');
                }
                $payment = $order->payments[0];
                // Make a charge attempt
                try {
                    if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                        // Payment type was payflow
                        $gateway_mapper->processReferenceTransaction(
                            $sub->product->cost, $payment->gateway_data->pnref); 
                    } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                        // Payment type was paypal
                        $gateway_mapper->processECReferenceTransaction(
                            $sub->product->cost, $payment->gateway_data->baid); 
                    } else {
                        throw new Exception('Error determining payment type');
                    }
                    $status = true;
                } catch (Exception $e2) {
                    $status = false;
                    $log_data['exceptions'] = $e2->getMessage() . ' ' .
                        $e2->getTraceAsString();
                    $logger->log('Recurring payment failed for ' .
                        $order->user->email . ' ' .
                        $e2->getMessage() . ' ' . $e2->getTraceAsString(),
                        Zend_Log::CRIT);
                }
                $log_data['gateway_calls'] = $gateway_mapper->getRawCalls();
                // Only save successful payment
                if ($status) {
                    // Save gateway data
                    $gateway_responses = $gateway_mapper->getSuccessfulResponseObjects();
                    foreach ($gateway_responses as $response) {
                        $payment_data = array(
                            'order_id'        => $order->id,
                            'amount'          => $sub->product->cost,
                            'date'            => date('Y-m-d H:i:s')
                        );
                        if (is_a($response, 'Model_PaymentGateway_Response_Payflow')) {
                            $payments_mapper->insert(array_merge($payment_data, array(
                                'payment_type_id' => Model_PaymentType::PAYFLOW,
                                'pnref'           => $response->pnref,
                                'ppref'           => $response->ppref,
                                'correlationid'   => $response->correlationid
                            )));
                        } elseif (is_a($response, 'Model_PaymentGateway_Response_Paypal')) {
                            $payments_mapper->insert(array_merge($payment_data, array(
                                'payment_type_id' => Model_PaymentType::PAYPAL,
                                'pnref'           => $response->pnref,
                                'correlationid'   => $response->correlationid
                            )));
                        }
                    }
                    // Calculate new expiration
                    $new_expiration = new DateTime($sub->expiration);
                    $date_int_str = "P{$sub->product->term_months}M";
                    $new_expiration->add(new DateInterval($date_int_str));
                    $digital_only = is_a($sub->product,
                        'Model_Product_DigitalSubscription');
                    $ops_mapper->insert(array(
                        'user_id'          => $order->user->id,
                        'order_product_id' => $sub->order_product_id,
                        'expiration'       => $new_expiration->format('Y-m-d'),
                        'digital_only'     => $digital_only 
                    ));
                }
                // Mail customer
                try {
                    if ($status) {
                        $tpl = 'emails/recurring_billing_success.phtml';
                    } else {
                        $tpl = 'emails/recurring_billing_fail.phtml';
                    }
                    $mail = new Zend_Mail;
                    $mail->setBodyText($view->render($tpl))
                         ->addTo($order->user->email)
                         ->setSubject('Customer invoice')
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                } catch (Exception $e3) {
                    // Log
                    $exception_str = $e3->getMessage() . ' ' .
                        $e3->getTraceAsString();
                    $logger->log('Recurring billing failure, mail not sent for ' .
                        $order->user->email . ' ' . 
                        $exception_str, Zend_Log::CRIT);
                }
                $rb_logger->insertTransaction($status, $log_data);
                // Important! Break the loop and cause this method to run again
                // if this is taking too damn long, since it blocks inserts
                // and updates
                if (time() - $start_time > 20) {
                    $run_again = true;
                    break;
                }
            } 
            $db->commit();
            //echo "elapsed time: " . (time() - $start_time) . "\n";
            if ($run_again) {
                $this->processRecurringBilling($expiration);
            }
        } catch (Exception $e) {
            try {
                // Void any transactions and log
                $exception_str = 'Urgent action necessary, recurring billing: ' .
                    $e->getMessage() . ' ' . $e->getTraceAsString();
                $logger->log($exception_str, Zend_Log::EMERG);
                $gateway_mapper->voidCalls();
                $rb_logger->insertErrors(false, array(
                    'gateway_calls' => $gateway_mapper->getRawCalls(),
                    'exceptions'    => $exception_str
                ));
            } catch (Exception $f) {}
        }
    }
}
