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
        'change #order-add #product': 'productChange',
        
        'click #order-detail .credit': 'openCreditPopup',
        'click #order-detail .payments td': 'adminTableRowClick',
        'hover #order-detail .payments td': 'adminTableRowHover',

        'click #credit-payment [name=credit-submit]': 'submitCreditForm',
        'click #credit-payment [name=credit-cancel]': 'closeCreditPopup',
        'click #credit-payment-success [name=credit-return]': 'closeCreditPopup'
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
    },

    openCreditPopup: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href'),
            afterClose: function() {
                window.location.href = window.location.href;
            }
        });
        return false;
    },

    submitCreditForm: function(el) {
        var qs = $('form[name=credit_payment]', this.el).serialize();
        var url = '/admin/payments/credit/id/' + $('#order_payment_id').val();
        //this.showSpinner();
        $(el.target).parent().find('input[type=submit]').attr('disabled', true);
        this.populateFancybox(url, qs);
        return false;
    },

    closeCreditPopup: function(el) {
        $.fancybox.close(); 
        window.location.href = window.location.href;
        return true;
    }

});

