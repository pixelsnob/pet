<?php
/**
 * Returns formatted dollar amount
 * 
 * @package Pet_View_Helper_DollarFormat
 * 
 */
class Pet_View_Helper_DollarFormat extends Pet_View_Helper_HeadScript {
    
    /**
     * @return string
     * 
     */
    public function dollarFormat($amount) {
        return '$' . number_format((float) $amount, 2, '.', ',');
    }
}
