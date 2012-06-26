/**
 * Admin orders view
 * 
 */
Pet.AdminOrdersView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click input[name=payment_method]': 'togglePaymentFields'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
        this.togglePaymentFields($('input[name=payment_method]:checked').get(0));
    },

    togglePaymentFields: function() {
        var payment_method = $('input[name=payment_method]:checked').val();
        if (payment_method == 'credit_card') {
            $('.form .check').hide();
            $('.form .cc').show();
            $('.form .amount').show();
        } else if (payment_method == 'check') {
            $('.form .check').show();
            $('.form .cc').hide();
            $('.form .amount').show();
        } else if (payment_method == 'bypass') {
            $('.form .check').hide();
            $('.form .cc').hide();
            $('.form .amount').hide();
        }
    }

});

