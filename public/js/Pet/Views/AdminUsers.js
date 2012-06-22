/**
 * Base admin view
 * 
 */
Pet.AdminUsersView = Pet.View.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #billing-to-shipping': 'copyBillingToShipping'
    },
    
    initialize: function() {
    },

    copyBillingToShipping: function() {
        $('.billing input, .billing select').each(function() {
            var suffix = $(this).attr('id').replace(/billing_/, '');
            $('#shipping_' + suffix).val($(this).val());
        });
        return false;
    }

});

