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
     * @param int $id
     * @return Model_Order
     * 
     */
    public function getFullOrder($id) {
        $op_mapper          = new Model_Mapper_OrderProducts;
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway_mapper     = new Model_Mapper_PaymentGateway;
        $users_mapper       = new Model_Mapper_Users;
        // Get order
        $order = $this->getById($id);
        if (!$order) {
            $msg = 'Error retrieving order';
            throw new Exception($msg);
        }
        $user = $users_mapper->getById($order->user_id);
        // Get user
        if (!$user) {
            $msg = 'Error retrieving user';
            throw new Exception($msg);
        }
        $order->user = $user;
        // Get order products
        $products = $op_mapper->getByOrderId($order->id);
        $temp_products = array();
        if ($products) {
            foreach ($products as $product) {
                $temp_products[] = $products_mapper->getById(
                    $product->product_id); 
            }
            $order->products = $temp_products;
        }
        // Get payment(s)
        $payments = $payments_mapper->getByOrderId($order->id); 
        if (!$payments) {
            $msg = 'Error retrieving from order_payments';
            throw new Exception($msg);
        }
        $order->payments = $payments;
        // Get subscriptions...

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
                $order_products = $op_mapper->getByOrderId($order->id);
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
     * This should be run once per day
     * 
     * @param DateTime $expiration The expiration date to use for the search
     * @return void
     * 
     */
    public function processRecurringBilling(DateTime $expiration) {
        $ops_mapper         = new Model_Mapper_OrderProductSubscriptions;
        $payflow_mapper     = new Model_Mapper_OrderPayments_Payflow; 
        $paypal_mapper      = new Model_Mapper_OrderPayments_Paypal; 
        $payments_mapper    = new Model_Mapper_OrderPayments;
        $products_mapper    = new Model_Mapper_Products;
        $gateway_mapper     = new Model_Mapper_PaymentGateway;
        $users_mapper       = new Model_Mapper_Users;
        $logger             = new Model_Mapper_RecurringBillingTransactions;
        $view               = Zend_Registry::get('view');
        $gateway_exceptions = array();
        $email_exceptions   = array();
        //$expiration      = new DateTime('2012-11-02');
        //$date->add(new DateInterval('P2D'));
        $db = Zend_Db_Table::getDefaultAdapter();
        try {
            $db->query('set transaction isolation level serializable');
            $db->beginTransaction();
            $subs = $ops_mapper->getByExpiration($expiration);
            foreach ($subs as $sub) {
                if (!$sub->product || !$sub->product->is_recurring) {
                    continue;
                }
                $status = true;
                $log_data = array(
                    'order'              => array(),
                    'gateway_calls'      => array(),
                    'exceptions'         => array()
                );
                $min_expiration = new DateTime($sub->min_expiration);
                // Stop repeating around a year -- reference transactions will only
                // last that long...
                if ($expiration->diff($min_expiration)->m > 11) {
                    continue; 
                }
                // Get order
                $order = $this->getFullOrder($sub->order_id);
                if (!$order) {
                    throw new Exception('Error retrieving order');
                }
                $log_data['order'] = $order;
                if (!isset($order->payments[0])) {
                    throw new Exception('Error retrieving order payment');
                }
                $payment = $order->payments[0];
                if ($payment->payment_type_id == Model_PaymentType::PAYFLOW) {
                    // Payment type was payflow
                    $origid = $payment->pnref;
                    $tender = 'C';
                } elseif ($payment->payment_type_id == Model_PaymentType::PAYPAL) {
                    // Payment type was paypal
                    $origid = $payment->pnref;
                    $tender = 'P';
                }
                // Make a charge attempt
                try {
                    $gateway_mapper->processReferenceTransaction(
                        $sub->product->cost, $origid, $tender); 
                } catch (Exception $e2) {
                    $status = false;
                    $log_data['exceptions'] = $e2->getMessage() . ' ' .
                        $e2->getTraceAsString();
                }
                $log_data['gateway_calls'] = $gateway_mapper->getRawCalls();
                // Mail customer
                try {
                    if ($status) {
                        $message = $view->render(
                            'emails/recurring_billing_success.phtml');
                    } else {
                        $message = $view->render(
                            'emails/recurring_billing_fail.phtml');
                    }
                    $mail = new Zend_Mail;
                    $mail->setBodyText($message)
                         ->addTo($order->user->email)
                         ->setSubject('Customer invoice')
                         ->addBcc('soapscum@pixelsnob.com')
                         ->send();
                    throw new Exception('shit');
                } catch (Exception $e3) {
                    // log
                    $log_data['exceptions'][] = $e3->getMessage() .
                        ' ' . $e3->getTraceAsString();
                }
                $gateway_responses = $gateway_mapper->getSuccessfulResponseObjects();
                // this should probably be moved to payments mapper
                foreach ($gateway_responses as $response) {
                    if (is_a($response, 'Model_PaymentGateway_Response_Payflow')) {
                        $opid = $payments_mapper->insert(array(
                            'order_id'            => $order->id,
                            'payment_type_id'     => Model_PaymentType::PAYFLOW,
                            'amount'              => $order->total,
                            'date'                => date('Y-m-d H:i:s')
                        ));
                        $payflow_mapper->insert(array(
                            'order_payment_id'    => $opid,
                            'pnref'               => $response->pnref,
                            'ppref'               => $response->ppref,
                            'correlationid'       => $response->correlationid,

                        ));
                    } elseif (is_a($response, 'Model_PaymentGateway_Response_Paypal')) {
                        $opid = $payments_mapper->insert(array(
                            'order_id'         => $order->id,
                            'payment_type_id'  => Model_PaymentType::PAYPAL,
                            'amount'           => $order->total,
                            'date'             => date('Y-m-d H:i:s')
                        ));
                        $paypal_mapper->insert(array(
                            'order_payment_id' => $opid,
                            'correlationid'    => $response->correlationid
                        ));
                    }
                }
                $logger->insert($status, $log_data);
                // update expiration
                // log
                //print_r($sub);
            } 
            $db->commit();
        } catch (Exception $e) {
            // log !!!!
            // void any transactions
            $gateway_mapper->voidCalls();
        }
    }
}
