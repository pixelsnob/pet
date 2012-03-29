<?php
/**
 * @package Model_Cart_Prod
 * 
 */
class Model_Cart_Prod extends Onone_Model_Abstract {
    
    /**
     * @var array
     * 
     */
    protected $_data = array(
        'qty'              => 0,
        'prod'             => null,
        'title'            => '',
        'version'          => '',
        'delivery_type'    => '',
        //'prod_line_offers' => array(),
        'serials'          => array(),
        'order_prod_id'    => 0
    );
    
    /**
     * Makes sure that prod property is only set to instance of Model_Prod
     * 
     * @return Model_Prod
     * 
     */
    public function setProd(Model_Prod $prod) {
        // Set default delivery type
        if (!$this->_data['delivery_type']) {
            if ($prod->prod_isDownloadable) {
                $this->_data['delivery_type'] = 'dl';
            } elseif ($prod->prod_isShippable) {
                $this->_data['delivery_type'] = 'box';
            }
        }
        return $prod;
    }
    
    /**
     * Returns the product's prodLine_nm
     * 
     * @return string
     * 
     */
    public function getProdLineNm() {
        return $this->prod->prodLine->prodLine_nm;
    }

    /**
     * @return array
     * 
     */
    public function toArray() {
        $data = $this->_data;
        if (is_a($this->_data['prod'], 'Model_Prod')) {
            $data['prod'] = $this->_data['prod']->toArray();
        }
        return $data;
    }
}
