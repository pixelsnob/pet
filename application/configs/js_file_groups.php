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
    )
);
