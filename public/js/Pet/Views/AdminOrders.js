/**
 * Admin orders view
 * 
 */
Pet.AdminOrdersView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #orders .admin-table td': 'adminTableRowClick',
        'hover #orders .admin-table td': 'adminTableRowHover',
        'click input[name=payment_method]': 'togglePaymentFields',
        'click #order-add .submit': 'overlayFormSubmit',
        'change #order-add #product': 'productChange'
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
    },

    productChange: function(el) {
        Backbone.emulateJSON = true;
        var cost_model = new Pet.ProductCostModel;
        cost_model.fetch({ data: { id: $(el.target).val() }});
        cost_model.on('change', function(model) {
            var cost = cost_model.get('cost');
            $('#amount').val(cost);
        });
    }

});

