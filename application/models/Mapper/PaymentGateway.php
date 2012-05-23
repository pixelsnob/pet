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
            'CLIENT_IP'     => $_SERVER['REMOTE_ADDR']
        );
        $gateway->setFields($fields)
            ->setUrl($gateway_config['url'])
            ->setHeader('X-VPS-Request-ID', $this->_getRequestId());
        $this->_gateway = $gateway;
    }
    
    /**
     * @param array $data
     * @return void
     * 
     */
    public function processSale(array $data) {
        $this->resetGateway();
        $data = $this->formatData($data);
        $this->_gateway->setSensitiveFields(array('ACCT', 'CVV2'))
            ->setField('TENDER', 'C')
            ->setField('TRXTYPE', 'S')
            ->setField('ACCT', $data['cc_num'])
            ->setField('CVV2', $data['cc_cvv'])
            ->setField('AMT', $data['total'])
            ->setField('EXPDATE', $data['exp_date'])
            ->setField('NAME', $data['name'])
            ->setField('STREET', $data['address'])
            ->setField('EMAIL', $data['email'])
            ->setField('ZIP', $data['billing_postal_code'])
            ->setField('CITY', $data['billing_city'])
            ->setField('STATE', $data['billing_state'])
            ->setField('SHIPTOFIRSTNAME', $data['shipping_first_name'])
            ->setField('SHIPTOLASTNAME', $data['shipping_last_name'])
            ->setField('SHIPTOSTREET', $data['shipping_address'])
            ->setField('SHIPTOZIP', $data['shipping_postal_code'])
            ->setField('SHIPTOCITY', $data['shipping_city'])
            ->setField('SHIPTOSTATE', $data['shipping_state'])
            ->setField('PHONENUM', $data['shipping_phone'])
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
     * @param array $data
     * @param string $return_url
     * @param string $cancel_url
     * 
     */
    public function getECToken(array $data, $return_url, $cancel_url) {
        $this->resetGateway();
        $this->_gateway->setField('AMT', $data['total'])
            ->setField('EMAIL', $data['email'])
            ->setField('TRXTYPE', 'S')
            ->setField('ACTION', 'S')
            ->setField('TENDER', 'P')
            ->setField('NOSHIPPING', 1)
            ->setField('RETURNURL', $return_url)
            ->setField('CANCELURL', $cancel_url)
            ->setField('ITEMAMT', $data['total'])
            // Ask about this
            ->setField('BA_DESC', 'Photoshop Elements User Subscription')
            ->setField('BILLING_TYPE', 'MerchantInitiatedBilling')
            ->setField('ITEMAMT', $data['total']);
        // Add line item info
        $i = 0;
        foreach ($data['products'] as $product) {
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
     * @param array $data
     * @param string $token
     * @param string $payer_id
     * @return void
     */
    public function processECSale(array $data, $token, $payer_id) {
        $this->resetGateway();
        $this->_gateway->setField('AMT', $data['total'])
              ->setField('EMAIL', $data['email'])
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
            $trxtype = (isset($call['request']['TRXTYPE']) ?
                $call['request']['TRXTYPE'] : '');
            // Only void successful transactions
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
     * @param array $data
     * @return array
     * 
     */
    public function formatData($data) {
        $data['exp_date'] = $data['cc_exp_month'] . substr($data['cc_exp_year'], 2, 2);
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['address'] = $data['billing_address'] . ' ' . $data['billing_address_2'];
        $data['shipping_address'] = $data['shipping_address'] . ' ' .
            $data['shipping_address_2'];
        return $data;
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
    /**
     * @param array $data
     * @param string $return_url
     * @param string $cancel_url
     * 
     */
    /*public function getECTokenRecurring(array $data, $return_url, $cancel_url,
                                        array $products) {
        $this->resetGateway();
        $this->_gateway->setField('AMT', $data['total'])
            ->setField('METHOD', 'SetExpressCheckout')
            ->setField('EMAIL', $data['email'])
            ->setField('TRXTYPE', 'A')
            ->setField('ACTION', 'S')
            ->setField('TENDER', 'P')
            //->setField('NOSHIPPING', 1)
            ->setField('RETURNURL', $return_url)
            ->setField('CANCELURL', $cancel_url);
        foreach ($products as $product) {
            $this->_gateway->setField('L_BILLINGTYPE0', 'RecurringPayments')
                ->setField('L_BILLINGAGREEMENTDESCRIPTION0', $product['name']);
        }
        $this->_gateway->send()->processResponse();
        $this->saveCall();
        if ($this->_gateway->isSuccess()) {
            return $this->_gateway->getResponseField('TOKEN');
        }  else {
            throw new Exception(__FUNCTION__ . '() failed');
        }
    }*/

    /**
     * @param array $data
     * @param string $token
     * @return void
     * 
     */
    /*public function processRecurringPayment(array $data) {
        $this->resetGateway();
        $data = $this->formatData($data);
        $start_date = new DateTime;
        $start_date->add(new DateInterval('P1M'));
        $this->_gateway->setSensitiveFields(array('ACCT', 'CVV2'))
            ->setField('TENDER', 'C')
            ->setField('TRXTYPE', 'R')
            ->setField('ACTION', 'A')
            ->setField('ACCT', $data['cc_num'])
            ->setField('CVV2', $data['cc_cvv'])
            ->setField('AMT', $data['cost'])
            //->setField('OPTIONALTRXAMT', $data['cost'])
            ->setField('EXPDATE', $data['exp_date'])
            ->setField('PROFILENAME', $data['profile_id'])
            ->setField('NAME', $data['name'])
            ->setField('STREET', $data['address'])
            ->setField('EMAIL', $data['email'])
            ->setField('ZIP', $data['billing_postal_code'])
            ->setField('CITY', $data['billing_city'])
            ->setField('STATE', $data['billing_state'])
            ->setField('SHIPTOFIRSTNAME', $data['shipping_first_name'])
            ->setField('SHIPTOLASTNAME', $data['shipping_last_name'])
            ->setField('SHIPTOSTREET', $data['shipping_address'])
            ->setField('SHIPTOZIP', $data['shipping_postal_code'])
            ->setField('SHIPTOCITY', $data['shipping_city'])
            ->setField('SHIPTOSTATE', $data['shipping_state'])
            ->setField('PHONENUM', $data['shipping_phone'])
            ->setField('START', $start_date->format('mdY'))
            // Subtract one from term since we will bill for 1st month
            // immediately
            ->setField('TERM', $data['term'] - 1)
            ->setField('PAYPERIOD', 'MONT')
            ->setField('MAXFAILPAYMENTS', 0)
            ->setField('L_BILLINGAGREEMENTDESCRIPTION0', $data['description']);
            //->setField('BA_DESC', $data['description']);
        $this->_gateway->send()->processResponse();
        $this->saveCall();
        if ($this->_gateway->isSuccess()) {
            if ($this->_gateway->getResponseField('CVV2MATCH') == 'N') {
                $this->_error = self::ERR_CVV;
                throw new Exception('CVV Mismatch');
            }
        } else {
            $msg = 'CC transaction failed.';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }*/

    /**
     * @param array $data
     * @param string $token
     * @param string $payer_id
     * @return void
     * 
     */
    /*public function processECRecurringPayment(array $data, $token, $payer_id) {
        $this->resetGateway();
        $data = $this->formatData($data);
        $start_date = new DateTime;
        $start_date->add(new DateInterval('P1M'));
        $this->_gateway->setField('TENDER', 'P')
            ->setField('TRXTYPE', 'R')
            ->setField('ACTION', 'A')
            ->setField('AMT', $data['cost'])
            // Bill this month immediately
            //->setField('OPTIONALTRXAMT', $data['cost'])
            ->setField('PROFILENAME', $data['profile_id'])
            ->setField('NAME', $data['name'])
            ->setField('STREET', $data['address'])
            ->setField('EMAIL', $data['email'])
            ->setField('ZIP', $data['billing_postal_code'])
            ->setField('CITY', $data['billing_city'])
            ->setField('STATE', $data['billing_state'])
            ->setField('SHIPTOFIRSTNAME', $data['shipping_first_name'])
            ->setField('SHIPTOLASTNAME', $data['shipping_last_name'])
            ->setField('SHIPTOSTREET', $data['shipping_address'])
            ->setField('SHIPTOZIP', $data['shipping_postal_code'])
            ->setField('SHIPTOCITY', $data['shipping_city'])
            ->setField('SHIPTOSTATE', $data['shipping_state'])
            ->setField('PHONENUM', $data['shipping_phone'])
            ->setField('START', $start_date->format('mdY'))
            // Subtract one from term since we just billed for 1st month
            ->setField('TERM', $data['term'] - 1)
            ->setField('PAYPERIOD', 'MONT')
            ->setField('MAXFAILPAYMENTS', 0)
            ->setField('TOKEN', $token)
            ->setField('PAYERID', $payer_id)
            ->setField('L_BILLINGAGREEMENTDESCRIPTION0', $data['description'])
            ->send()->processResponse();
        $this->saveCall();
        // Check to see if response failed.
        if (!$this->_gateway->isSuccess()) {
            $msg = __FUNCTION__ . '() failed';
            if ($this->_gateway->getError()) {
                $msg .= ' Gateway error: ' . $this->_gateway->getError();
                $this->_error = self::ERR_GENERIC;
            } else {
                $this->_error = self::ERR_DECLINED;
            }
            throw new Exception($msg);
        }
    }*/
}
