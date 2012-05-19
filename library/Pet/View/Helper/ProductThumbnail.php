<?php
/** 
 * @package Pet_View_Helper_ProductThumbnail
 * 
 */
class Pet_View_Helper_ProductThumbnail extends Zend_View_Helper_Abstract {
    
    /**
     * @return mixed
     * 
     */
    public function productThumbnail(Model_Product_Abstract $product,
                                     $size = 'small') {
        if ($product->isGift()) {
            $img = 'gifts.jpg';
        } else {
            switch ($product->product_type_id) {
                case Model_ProductType::SUBSCRIPTION:
                    $img = 'subscriptions.jpg';
                    break;
                case Model_ProductType::DIGITAL_SUBSCRIPTION:
                    $img = 'digital.jpg';
                    break;
                case Model_ProductType::PHYSICAL:
                    $img = 'dvds.jpg';
                    break;
            }
        }
        switch ($size) {
            case 'small':
                $w = 90;
                $h = 96;
                break;
            case 'large':
                $w = 435;
                $h = 462;
                break;
        }
        if (isset($img)) {
            return "<img src=\"/images/product-types/$size/$img\" " .
                "width=\"$w\" height=\"$h\">";
        }
    }
}

