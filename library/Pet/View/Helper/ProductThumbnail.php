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
    public function productThumbnail(Model_Cart_Product $product,
                                     $size = 'small') {
        if (($product->isSubscription() || $product->isDigital()) &&
                $product->isGift()) {
            $img = 'gifts.jpg';
        } else {
            if ($product->isSubscription()) {
                $img = 'subscriptions.jpg';
            } elseif ($product->isDigital()) {
                $img = 'digital.jpg';
            } elseif ($product->isPhysical()) {
                $img = 'dvds.jpg';
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

