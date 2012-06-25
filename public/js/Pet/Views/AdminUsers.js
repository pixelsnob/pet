/**
 * Base admin view
 * 
 */
Pet.AdminUsersView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #billing-to-shipping': 'copyBillingToShipping',
        'click #change_password': 'togglePasswordFields'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        if ($('form[name=user_edit]').length) {
            if ($('#change_password:checked').length) {
                $('.form dd.pw, .form dt.pw').show();
            } else {
                $('.form dd.pw, .form dt.pw').hide();
            }
        }
    },

    copyBillingToShipping: function() {
        $('.billing input, .billing select').each(function() {
            var suffix = $(this).attr('id').replace(/billing_/, '');
            $('#shipping_' + suffix).val($(this).val());
        });
        return false;
    },

    togglePasswordFields: function(el) {
        if (el.target.checked) {
            $('.form dd.pw, .form dt.pw').show();
        } else {
            $('.form dd.pw, .form dt.pw').hide();
        }
    }

});

