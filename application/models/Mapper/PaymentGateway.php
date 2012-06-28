<?php
/**
 * @package Model_Mapper_PaymentGateway
 * 
 */
require 'PayPal.php';

class Model_Mapper_PaymentGateway extends Pet_Model_Mapper_Abstract {
    
    /**
     * @var PayPal
     * 
     */
    private $_gateway;
    
    /**
     * @var array 
     * 
     */
    private $_calls = array();

    /**
     * @var string
     * 
     */
    private $_error = '';

    /**
     * Constants
     * 
     */
    const ERR_GENERIC          = 'err_generic',
          ERR_DECLINED         = 'err_declined',
          ERR_CVV              = 'err_cvv',
          ERR_EXPRESS_CHECKOUT = 'err_express_checkout';

    /**
     * @param Zend_Config $config
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * Creates a new gateway object and assigns it to $_gateway
     * 
     * @return void
     */
    public function resetGateway() {
        $gateway = new PayPal;
        $app_config = Zend_Registry::get('app_config');
        $gateway_config = $app_config['payment_gateway'];
        $fields = array(
            'USER'          => $gateway_config['user'],
            'PWD'           => $gateway_config['pwd'],
            'VENDOR'        => $gateway_config['vendor'],
            'PARTNER'       => $gateway_config['partner'],
            'VERBOSITY'     => 'medium',
            'CLIENT_IP'     => (isset($_SERVER['REMOTE_ADDR']) ?
                               $_SERVER['REMOTE_ADDR'] : '')
        );
        $gateway->setFields($fields)
            ->setUrl($gateway_config['url'])
            ->setHeader('X-VPS-Request-ID', $this->_getRequestId());
        $this->_gateway = $gateway;
    }
    
    /**
     * @param Model_Cart_Order
     * @return void
     * 
     */
    public function processSale(Model_Cart_Order $order) {
        $this->resetGateway();
        $order = $this->formatData($order);
        $this->_gateway->setSensitiveFields(array('ACCT', 'CVV2'))
            ->setField('TENDER', 'C')
            ->setField('TRXTYPE', 'S')
            ->setField('ACCT', $order->cc_num)
            ->setField('CVV2', $order->cc_cvv)
            ->setField('AMT', $order->total)
            ->setField('EXPDATE', $order->cc_exp)
            ->setField('NAME', $order->first_name . ' ' . $order->last_name)
            ->setField('STREET', $order->billing_address)
            ->setField('EMAIL', $order->email)
            ->setField('ZIP', $order->billing_postal_code)
            ->setField('CITY', $order->billing_city)
            ->setField('STATE', $order->billing_state)
            ->setField('SHIPTOFIRSTNAME', $order->shipping_first_name)
            ->setField('SHIPTOLASTNAME', $order->shipping_last_name)
            ->setField('SHIPTOSTREET', $order->shipping_address)
            ->setField('SHIPTOZIP', $order->shipping_postal_code)
            ->setField('SHIPTOCITY', $order->shipping_city)
            ->setField('SHIPTOSTATE', $order->shipping_state)
            ->setField('PHONENUM', $order->shipping_phone)
            ->send()
            ->processResponse();
        $this->saveCall();
        if ($this->_gateway->isSuccess()) {
            if ($this->_gateway->getResponseField('CVV2MATCH') == 'N') {
                $this->_error = self::ERR_CVV;
                throw new Exception('CVV Mismatch');
            }
        } else {
            $msg = __FUNCTION__ . '() failed.';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }

    /**
     * @param Model_Cart_Order
     * @param string $return_url
     * @param string $cancel_url
     * 
     */
    public function getECToken(Model_Cart_Order $order, $return_url, $cancel_url) {
        $this->resetGateway();
        $this->_gateway->setField('AMT', $order->total)
            ->setField('EMAIL', $order->email)
            ->setField('TRXTYPE', 'S')
            ->setField('ACTION', 'S')
            ->setField('TENDER', 'P')
            ->setField('NOSHIPPING', 1)
            ->setField('RETURNURL', $return_url)
            ->setField('CANCELURL', $cancel_url)
            ->setField('ITEMAMT', $order->total)
            // Ask about this
            ->setField('BA_DESC', 'Photoshop Elements User Subscription')
            ->setField('BILLINGTYPE', 'MerchantInitiatedBilling')
            ->setField('PAYMENTTYPE', 'any');
            //->setField('L_BILLINGTYPE0', 'MerchantInitiatedBilling');
            //->setField('ITEMAMT', $order->total);
        // Add line item info
        $i = 0;
        foreach ($order->products as $product) {
            $this->_gateway->setField("L_NAME$i", $product['name'])
                ->setField("L_QTY$i", $product['qty'])
                ->setField("L_COST$i", $product['cost']);
            $i++;
        }
        $this->_gateway->send()->processResponse();
        $this->saveCall();
        if ($this->_gateway->isSuccess()) {
            return $this->_gateway->getResponseField('TOKEN');
        }  else {
            throw new Exception(__FUNCTION__ . '() failed');
        }
    }

    /**
     * Processes an Express Checkout sale
     * 
     * @param Model_Cart_Order
     * @param string $token
     * @param string $payer_id
     * @return void
     */
    public function processECSale(Model_Cart_Order $order, $token, $payer_id) {
        $this->resetGateway();
        $this->_gateway->setField('AMT', $order->total)
              ->setField('EMAIL', $order->email)
              ->setField('TRXTYPE', 'S')
              ->setField('ACTION', 'D')
              ->setField('TENDER', 'P')
              ->setField('TOKEN', $token)
              ->setField('PAYERID', $payer_id)
              ->send()
              ->processResponse();
        $this->saveCall();
        if (!$this->_gateway->isSuccess()) {
            $this->_error = self::ERR_EXPRESS_CHECKOUT;
            throw new Exception('Express checkout process failed');
        }
    }

    /**
     * @param float $amount
     * @param string $id The original transaction id
     * @return void
     * 
     */
    public function processReferenceTransaction($amount, $origid) {
        $this->resetGateway();
        $this->_gateway
            ->setField('TENDER', 'C')
            ->setField('AMT', $amount)
            ->setField('TRXTYPE', 'S')
            ->setField('ORIGID', $origid);
        $this->_gateway->send()->processResponse();
        $this->saveCall();
        if (!$this->_gateway->isSuccess()) {
            $msg = __FUNCTION__ . '() failed.';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }

    /**
     * @param float $amount
     * @param string $baid The original billing agreement id
     * @return void
     * 
     */
    public function processECReferenceTransaction($amount, $baid) {
        $this->resetGateway();
        $this->_gateway
            ->setField('TENDER', 'P')
            ->setField('AMT', $amount)
            ->setField('BAID', $baid)
            ->setField('TRXTYPE', 'S')
            ->setField('ACTION', 'D');
        $this->_gateway->send()->processResponse();
        $this->saveCall();
        if (!$this->_gateway->isSuccess()) {
            $msg = __FUNCTION__ . '() failed.';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }
    
    /**
     * Processes a credit
     * 
     * @param string $orig_id
     * @param float $amount
     * @return void 
     */
    public function processCredit($orig_id, $amount) {
        $this->resetGateway();
        $this->_gateway->setField('ORIGID', $orig_id)
            ->setField('AMT', $amount)
            ->setField('TRXTYPE', 'C')
            ->send()
            ->processResponse();
        $this->saveCall();
        if (!$this->_gateway->isSuccess()) {
            $msg = __FUNCTION__ . '() failed.';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }
    
    /**
     * Processes a credit card void.
     * 
     * @return void 
     */
    public function processVoid($origid) {
        $this->resetGateway();
        $this->_gateway->setField('ORIGID', $origid)
            ->setField('TRXTYPE', 'V')
            ->send()
            ->processResponse();
        $this->saveCall();
        $result = $this->_gateway->getResponseField('RESULT');
        // 108 means the funds have already settled, so we need to do a credit
        if ($result == 108) {
            $this->resetGateway();
            $this->_gateway->setField('ORIGID', $origid)
                ->setField('TRXTYPE', 'C')
                ->send()
                ->processResponse();
            $this->saveCall();
            if (!$this->_gateway->isSuccess()) {
                $msg = 'Void/credit failed';
                if ($this->_gateway->getError()) {
                    $msg .= ' Gateway error: ' . $this->_gateway->getError();
                }
                throw new Exception($msg);
            }
        }
    }
    
    /**
     * Saves calls to the gateway for logging
     * 
     * @return void
     */
    public function saveCall() {
        $this->_calls[] = array(
            'request'      => $this->_gateway->getCleanedRequestAsArray(),
            'raw_request'  => $this->_gateway->getCleanedRequest(),
            'response'     => $this->_gateway->getResponse(),
            'raw_response' => $this->_gateway->getRawResponse()
        );
    }

    /**
     * Public accessor to $_calls
     * 
     * @return array
     */
    public function getRawCalls() {
        return $this->_calls;
    }
    
    /**
     * @return array
     * 
     */
    private function _getSuccessfulCalls() {
        $calls = array();
        foreach ($this->_calls as $call) {
            $result = (isset($call['response']['RESULT']) ?
                ((string) $call['response']['RESULT']) : null);
            if ($result !== '0') {
                continue;
            }
            $calls[] = $call;
        }
        return $calls;
    }
    
    /** 
     * Builds an array of successful response objects
     * 
     * @return array
     */
    public function getSuccessfulResponseObjects() {
        $calls = $this->_getSuccessfulCalls();
        $objs = array();
        foreach ($calls as $call) {
            $request = $call['request'];
            $response = $call['response'];
            $tender = (isset($call['request']['TENDER']) ?
                $call['request']['TENDER'] : '');
            switch ($tender) {
                case 'P':
                    $ro = new Model_PaymentGateway_Response_Paypal;
                    foreach ($ro->toArray() as $k => $v) {
                        $uk = strtoupper($k);
                        $ro->{$k} = (isset($response[$uk]) ? $response[$uk] :
                            null);
                    }
                    $objs[] = $ro;
                    break;
                case 'C':
                default:
                    $ro = new Model_PaymentGateway_Response_Payflow;
                    foreach ($ro->toArray() as $k => $v) {
                        $uk = strtoupper($k);
                        $ro->{$k} = (isset($response[$uk]) ? $response[$uk] :
                            null);
                    }
                    $objs[] = $ro;
                    break;
            }
        }
        return $objs;
    }

    /**
     * Voids all previous successful calls
     * 
     * @return void
     */
    public function voidCalls() {
        $trxtypes = array('S', 'A', 'D');
        $calls = $this->_getSuccessfulCalls();
        foreach ($calls as $call) {
            $trxtype = (isset($call['request']['TRXTYPE']) ?
                $call['request']['TRXTYPE'] : '');
            // Only void charges
            if (!in_array($trxtype, $trxtypes)) {
                continue;
            }
            $pnref = (isset($call['response']['PNREF']) ?
                $call['response']['PNREF'] : '');
            $this->processVoid($pnref);
        }
    }

    /**
     * @return string
     * 
     */
    public function getError() {
        return $this->_error;
    }
    
    /**
     * @param Model_Cart_Order $order
     * @return array
     * 
     */
    public function formatData(Model_Cart_Order $order) {
        $order->cc_exp  = $order->cc_exp_month  . substr($order->cc_exp_year , 2, 2);
        return $order;
    }

    /**
     * Gets a unique request id for the gateway request.
     * 
     * @return string 
     */
    private function _getRequestId() {
        $temp_strings = array(
            uniqid(),
            Zend_Session::getId()
        );
        return md5(implode('', $temp_strings));
    }
}
