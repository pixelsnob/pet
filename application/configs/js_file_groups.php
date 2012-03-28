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
        'js/Pet/Pet.js'
    ),
    'profile' => array(
        'js/Pet/Models/Auth.js',
        'js/Pet/Views/ProfileForm.js'
    )
);
