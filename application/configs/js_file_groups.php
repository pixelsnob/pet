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
        'js/lib/jquery.fancybox.pack.js',
        'js/lib/jquery.tools.min.js',
        'js/Pet/Pet.js',
        'js/Pet/Views/Base.js'
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
        'js/Pet/Models/Cart.js',
        'js/Pet/Models/Checkout.js',
        'js/Pet/Models/PromoCode.js',
        'js/Pet/Views/Checkout.js'
    ),
    'admin-common' => array(
        'js/lib/jquery-1.7.1.min.js',
        'js/lib/underscore-min.js',
        'js/lib/backbone-min.js',
        'js/lib/jquery.fancybox.pack.js',
        'js/lib/jquery.tools.min.js',
        'js/lib/jquery-ui.custom.min.js',
        'js/Pet/Pet.js',
        'js/Pet/Views/Base.js',
        'js/Pet/Views/Admin.js'
    ),
    'admin-users' => array(
        'js/Pet/Views/AdminUsers.js'
    ),
    'admin-orders' => array(
        'js/Pet/Models/ProductCost.js',
        'js/Pet/Views/AdminOrders.js'
    ),
    'admin-payments' => array(
        'js/Pet/Views/AdminPayments.js'
    ),
    'admin-products' => array(
        'js/Pet/Views/AdminProducts.js'
    )
);
