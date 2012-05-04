<?php
/**
 * @package Model_Mapper_Payment
 * 
 */
require 'PayPal.php';

class Model_Mapper_Cart_Payment extends Onone_Model_Mapper_Abstract {
    
    /**
     * @var string 
     * 
     */
    private $_auth_pnref;

    /**
     * @var string 
     * 
     */
    private $_capture_pnref;

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
        $app_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV);
        $this->_config = $app_config->payment_gateway;
        $cart_mapper = new Model_Mapper_Cart;
        $this->_cart = $cart_mapper->get();
        parent::__construct();
    }
    
    /**
     * Creates a new gateway object and assigns it to $_gateway
     * 
     * @return void
     */
    public function resetGateway() {
        $gateway = new PayPal;
        $fields = array(
            'USER'          => $this->_config->user,
            'PWD'           => $this->_config->pwd,
            'VENDOR'        => $this->_config->vendor,
            'PARTNER'       => $this->_config->partner,
            'VERBOSITY'     => 'medium',
            'CLIENT_IP'     => $_SERVER['REMOTE_ADDR']
        );
        // Ugly hack due to PayPal's Express Checkout testing url not working
        // anymore: use the live url for testing (!)
        $config_all = new Zend_Config_Ini(APPLICATION_PATH .
            '/configs/application.ini');
        if ($this->_cart->payment->payment_method == 'paypal' && 
            APPLICATION_ENV != 'production') {
            $url = $config_all->production->payment_gateway->url; 
        } else {
            $url = $this->_config->url;
        }
        $gateway->setFields($fields)
            ->setUrl($url)
            ->setHeader('X-VPS-Request-ID', $this->_getRequestId());
        $this->_gateway = $gateway;
    }

    /**
     * Gets an express checkout URL by making a gateway call to PayPal, and
     * retrieving some data.
     * 
     * 
     */
    public function getExpressCheckoutToken($return_url, $cancel_url) {
        $trxtype = ($this->_cart->hasBoxProds() ? 'A' : 'S');
        $this->resetGateway();
        $this->_gateway->setField('AMT', $this->_cart->totals->total)
            ->setField('EMAIL', $this->_cart->billing->email)
            ->setField('TRXTYPE', $trxtype)
            ->setField('ACTION', 'S')
            ->setField('TENDER', 'P')
            ->setField('NOSHIPPING', 1)
            ->setField('RETURNURL', $return_url)
            ->setField('CANCELURL', $cancel_url)
            ->send()
            ->processResponse();
        $this->saveCall();
        if ($this->_gateway->isSuccess()) {
            return $this->_gateway->getResponseField('TOKEN');
        }  else {
            return false;
        }
    }

    /**
     * Processes an Express Checkout transaction.
     * 
     * 
     */
    public function processExpressCheckout($token, $payer_id) {
        $trxtype = ($this->_cart->hasBoxProds() ? 'A' : 'S');
        $this->resetGateway();
        $this->_gateway->setField('AMT', $this->_cart->totals->total)
              ->setField('EMAIL', $this->_cart->billing->email)
              ->setField('TRXTYPE', $trxtype)
              ->setField('ACTION', 'D')
              ->setField('TENDER', 'P')
              ->setField('TOKEN', $token)
              ->setField('PAYERID', $payer_id)
              ->send()
              ->processResponse();
        $this->saveCall();
        $pnref = $this->_gateway->getResponseField('PNREF');
        if ($trxtype == 'A') {
            $this->_auth_pnref = $pnref;
        } else {
            $this->_capture_pnref = $pnref;
        }
        if (!$this->_gateway->isSuccess()) {
            $this->_error = self::ERR_EXPRESS_CHECKOUT;
            throw new Exception('Express checkout process failed');
        }
        return true;
    }

    /**
     * Performs an auth and a delayed capture credit card transaction.
     * 
     * 
     */
    public function processAuth() {
        // Put together some params.
        $exp_date = $this->_cart->payment->cc_exp_month .
            substr($this->_cart->payment->cc_exp_year, 2);
        $bill_name = $this->_cart->billing->bill_first_nm . ' ' .
            $this->_cart->billing->bill_last_nm;
        $ship_name = $this->_cart->shipping->ship_first_nm . ' ' .
            $this->_cart->shipping->ship_last_nm;
        $bill_addr = $this->_cart->billing->bill_addr1 . ' ' .
            $this->_cart->billing->bill_addr2;
        $ship_addr = $this->_cart->shipping->ship_addr1 . ' ' .
            $this->_cart->shipping->ship_addr2;
        // Gateway auth call.
        $this->resetGateway();
        $this->_gateway->setSensitiveFields(array('ACCT', 'CVV2'))
            ->setField('TENDER', 'C')
            ->setField('TRXTYPE', 'A')
            ->setField('ACCT', $this->_cart->payment->cc_num)
            ->setField('CVV2', $this->_cart->payment->cc_cvv)
            ->setField('AMT', $this->_cart->totals->total)
            ->setField('EXPDATE', $exp_date)
            ->setField('NAME', $bill_name)
            ->setField('STREET', $bill_addr)
            ->setField('EMAIL', $this->_cart->billing->email)
            ->setField('ZIP', $this->_cart->billing->bill_zip)
            ->setField('CITY', $this->_cart->billing->bill_city)
            ->setField('STATE', $this->_cart->billing->bill_state)
            ->setField('SHIPTOFIRSTNAME', $this->_cart->shipping->ship_first_nm)
            ->setField('SHIPTOLASTNAME', $this->_cart->shipping->ship_last_nm)
            ->setField('SHIPTOSTREET', $ship_addr)
            ->setField('SHIPTOZIP', $this->_cart->shipping->ship_zip)
            ->setField('SHIPTOCITY', $this->_cart->shipping->ship_city)
            ->setField('SHIPTOSTATE', $this->_cart->shipping->ship_state)
            ->setField('PHONENUM', $this->_cart->billing->bill_phone)
            ->send()
            ->processResponse();
        $this->saveCall();
        // Store PNREF value from the auth call.
        $this->_auth_pnref = $this->_gateway->getResponseField('PNREF');
        // Check to see if response failed.
        if ($this->_gateway->isSuccess()) {
            // If this is a CVV mismatch, void the auth.
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
    }
    
    /**
     * Makes a delayed capture call
     * 
     * @return void
     */
    public function processDelayedCapture() {
        $this->resetGateway();
        $this->_gateway->setField('TENDER', 'C')
            ->setField('ORIGID', $this->_auth_pnref)
            ->setField('TRXTYPE', 'D')
            ->send()
            ->processResponse();
        $this->saveCall();
        // Store pnref from capture.
        $this->_capture_pnref = $this->_gateway->getResponseField('PNREF');
        // If for some reason capture failed, void the original auth call.
        if (!$this->_gateway->isSuccess()) {
            $msg = 'Delayed capture failed';
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
    public function processVoid() {
        // Nothing to void
        if (!$this->_capture_pnref && !$this->_auth_pnref) {
            return;
        }
        $this->resetGateway();
        $pnref = ($this->_capture_pnref ? $this->_capture_pnref :
            $this->_auth_pnref);
        $this->_gateway->setField('ORIGID', $pnref)
            ->setField('TRXTYPE', 'V')
            ->send()
            ->processResponse();
        $this->saveCall();
        $result = $this->_gateway->getResponseField('RESULT');
        if ($this->_capture_pnref && $result == 108) {
            $this->resetGateway();
            $this->_gateway->setField('ORIGID', $pnref)
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
    public function getCalls() {
        return $this->_calls;
    }
    
    /**
     * Public accessor to $_auth_pnref
     * 
     * @return string
     */
    public function getAuthPnref() {
        return $this->_auth_pnref;
    }
    
    /**
     * Public accessor to $_capture_pnref
     * 
     * @return string
     */
    public function getCapturePnref() {
        return $this->_capture_pnref;
    }
    
    /**
     * @return string
     * 
     */
    public function getError() {
        return $this->_error;
    }

    /**
     * Gets a unique request id for the gateway request.
     * 
     * @return string 
     */
    private function _getRequestId() {
        $temp_strings = array(
            $this->_cart->payment->cc_num,
            $this->_cart->totals->total,
            $this->_cart->payment->cc_cvv,
            microtime(),
            Zend_Session::getId()
        );
        return md5(implode('', $temp_strings));
    }
}
