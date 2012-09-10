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
        $this->_orders_mapper = new Model_Mapper_Orders;
    }
    
    /**
     * @param int $order_id
     * @param bool $debug
     * @return void
     * 
     */
    public function sendOrderEmail($order_id, $debug = false) {
        $view = Zend_Registry::get('view');
        $app_config = Zend_Registry::get('app_config');
        $users_svc = new Service_Users;
        $order = $this->_orders_mapper->getFullOrder($order_id);
        $show_shipping = false;
        $has_subscription = false;
        $expirations = null;
        foreach ($order->products as $product) {
            if ($product->isGift()) {
                continue;
            }
            if ($product->isSubscription() || $product->isPhysical()) {
                $show_shipping = true;
            }
            if ($product->isSubscription()) {
                $has_subscription = true;
                $expirations = $users_svc->getExpirations($order->user_id);
            }
        }
        $message = $view->partial('emails/order.phtml', array(
            'expirations'      => $expirations,
            'show_shipping'    => $show_shipping,
            'has_subscription' => $has_subscription,
            'order'            => $order
        ));
        if (!$debug) {
            $mail = new Zend_Mail;
            $mail->setBodyText($message)
                 ->addTo($order->email)
                 ->setSubject('Photoshop Elements User Order: ' . $order->id);
            if ($app_config['order_emails']['bcc']) {
                $mail->addBcc($app_config['order_emails']['bcc']);
            }
            $mail->send();
        } else {
            echo $message . "\n\n";
        }
        if ($order->gifts) {
            foreach ($order->gifts as $gift) {
                $gift_message = $view->partial('emails/gift.phtml', array(
                    'gift'     => $gift,
                    'order'    => $order,
                    'base_url' => $app_config['base_url']
                ));
                if (!$debug) {
                    $mail = new Zend_Mail;
                    $mail->setBodyText($gift_message)
                         ->addTo($order->email)
                         ->setSubject('Your Photoshop Elements Techniques Gift Code is here!');
                    if ($app_config['order_emails']['bcc']) {
                        $mail->addBcc($app_config['order_emails']['bcc']);
                    }
                    $mail->send();
                } else {
                    echo $gift_message . "\n\n";
                }
            }
        }
    }

    /**
     * @param int $order_id
     * @param float $credit_amount
     * @param bool $debug
     * @return void
     * 
     */
    public function sendOrderCreditEmail($order_id, $credit_amount, $debug = false) {
        $view = Zend_Registry::get('view');
        $app_config = Zend_Registry::get('app_config');
        $order = $this->_orders_mapper->getFullOrder($order_id);
        $message = $view->partial('emails/order_credit.phtml', array(
            'order'         => $order,
            'credit_amount' => $credit_amount
        ));
        if (!$debug) {
            $mail = new Zend_Mail;
            $mail->setBodyText($message)
                 ->addTo($order->email)
                 ->setSubject('Photoshop Elements Refund, Order: ' . $order->id);
            if ($app_config['order_emails']['bcc']) {
                $mail->addBcc($app_config['order_emails']['bcc']);
            }
            $mail->send();
        } else {
            echo $message . "\n\n";
        }
    }


    /**
     * **** This is currently not in use ****
     * 
     * This should be run once per day, preferably not too long after midnight...
     * 
     * @param DateTime $expiration The expiration date to use for the search
     * @return void
     * 
     */
    /*public function processRecurringBilling(DateTime $expiration) {
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway            = new Model_Mapper_PaymentGateway;
        $gateway_logger     = new Model_Mapper_PaymentGateway_Logger_RecurringBilling;
        $view               = Zend_Registry::get('view');
        $logger             = Zend_Registry::get('log');
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
                $exceptions = array();
                $min_expiration = new DateTime($sub->min_expiration);
                // Stop repeating around a year -- reference transactions will only
                // last that long...
                if ($expiration->diff($min_expiration)->days > 335) {
                    // notify???????????????????????????
                    continue; 
                }
                // Get order
                $order = $this->_orders_mapper->getFullOrder($sub->order_id);
                if (!$order) {
                    throw new Exception('Error retrieving order');
                }
                if (!isset($order->payments[0])) {
                    throw new Exception('Error retrieving order payment');
                }
                $payment = $order->payments[0];
                // Make a charge attempt
                try {
                    if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                        // Payment type was payflow
                        $gateway->processReferenceTransaction(
                            $sub->product->cost, $payment->gateway_data->pnref); 
                    } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                        // Payment type was paypal
                        $gateway->processECReferenceTransaction(
                            $sub->product->cost, $payment->gateway_data->baid); 
                    } else {
                        throw new Exception('Error determining payment type');
                    }
                    $status = true;
                } catch (Exception $e2) {
                    $status = false;
                    $exceptions[] = $e2->getMessage() . ' ' .
                        $e2->getTraceAsString();
                    $logger->log('Recurring payment failed for ' .
                        $order->user->email . ' ' .
                        $e2->getMessage() . ' ' . $e2->getTraceAsString(),
                        Zend_Log::CRIT);
                }
                // Only save successful payment
                if ($status) {
                    // Save gateway data
                    $gateway_responses = $gateway->getSuccessfulResponseObjects();
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
                    $exceptions[] = $exception_str;
                    $logger->log('Recurring billing failure, mail not sent for ' .
                        $order->user->email . ' ' . 
                        $exception_str, Zend_Log::CRIT);
                }
                $gateway_logger->insert(
                    $status,
                    $order->toArray(),
                    $gateway->getRawCalls(),
                    $exceptions
                );
                // Important! Break the loop and cause this method to run again
                // if this is taking too damn long, since it blocks inserts
                // and updates
                if (time() - $start_time > 20) {
                    $run_again = true;
                    break;
                }
            } 
            throw new Exception('uh oh');
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
                $gateway->voidCalls();
                $gateway_logger->insert(
                    false,
                    $order->toArray(),
                    $gateway->getRawCalls(),
                    array($exception_str)
                );
            } catch (Exception $f) {}
        }
    }*/
}
