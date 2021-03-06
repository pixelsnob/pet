<?php
/**
 * An array of JS file groups, used to load dependencies in different
 * files, etc.
 * 
 */
return array(
    'common' => array(
        'js/lib/jquery-1.7.1.min.js',
        'js/lib/underscore-min.js',
        'js/lib/backbone-min.js',
        'js/lib/jquery.fancybox.js',
        'js/lib/jquery.tools.min.js',
        'js/Pet/Pet.js',
        'js/Pet/Views/Base.js'
        //'js/Pet/Views/Global.js'
    ),
    'profile' => array(
        'js/Pet/Models/Auth.js',
        'js/Pet/Views/ProfileForm.js'
    ),
    'products' => array(
        'js/Pet/Views/Products.js',
        'js/Pet/Models/Cart.js',
        'js/Pet/Views/Cart.js'
    ),
    'cart' => array(
        'js/Pet/Models/Cart.js',
        'js/Pet/Views/Cart.js'
    ),
    'checkout' => array(
        'js/lib/showdown.js',
        'js/Pet/Models/Cart.js',
        'js/Pet/Models/Checkout.js',
        'js/Pet/Models/PromoCode.js',
        'js/Pet/Views/Checkout.js'
    ),
    'confirmation' => array(
        'js/Pet/Views/Confirmation.js'
    ),
    'admin-common' => array(
        'js/lib/jquery-1.7.1.min.js',
        'js/lib/underscore-min.js',
        'js/lib/backbone-min.js',
        'js/lib/jquery.fancybox.js',
        'js/lib/jquery.tools.min.js',
        'js/lib/jquery-ui.custom.min.js',
        'js/Pet/Pet.js',
        'js/Pet/Views/Base.js',
        'js/Pet/Views/Admin/Base.js'
    ),
    'admin-users' => array(
        'js/Pet/Views/Admin/Users.js'
    ),
    'admin-orders' => array(
        'js/Pet/Models/ProductCost.js',
        'js/Pet/Views/Admin/Orders.js'
    ),
    'admin-payments' => array(
        'js/Pet/Views/Admin/Payments.js'
    ),
    'admin-products' => array(
        'js/Pet/Views/Admin/Products.js'
    ),
    'admin-shipping-zones' => array(
        'js/Pet/Views/Admin/ShippingZones.js'
    ),
    'admin-promos' => array(
        'js/Pet/Views/Admin/Promos.js'
    )
);
