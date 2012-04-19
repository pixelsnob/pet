/**
 * Products view
 * 
 */
Pet.ProductsView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=subscription-select-term] .submit input':
            'submitSubscriptionTermSelectForm',
        'click form[name=digital-subscription-select] .submit input':
            'submitDigitalSelectForm',
        'click form[name=login] .submit input':
            'submitLoginForm',
        'click .forgot-password a': 'resetPasswordRequestForm',
        'click form[name=reset-password-request] .submit input':
            'submitResetPasswordRequestForm',
        'click #subscription-zones li a, .digital-subscriptions a':
            'openSubscriptionSelectPopup',
        'click .renew': 'openRenewPopup'
    },
    
    initialize: function(){
        this.cart_view = new Pet.CartView;
    },
    
    openSubscriptionSelectPopup: function(el) {
        this.showFancybox({ href: $(el.target).attr('href') });
        return false;
    },

    openRenewPopup: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href') + '?redirect_params[nolayout]=1'
            /*afterClose: function() {
                window.location.href = window.location.href;
            }*/
        });
        return false;
    },

    submitSubscriptionTermSelectForm: function() {
        var obj = this;
        var qs = $('form[name=subscription-select-term]', this.el).serialize();
        this.populateFancyboxPost('/products/subscription/term', qs);
        return false; 
    },

    submitDigitalSelectForm: function() {
        var obj = this;
        var qs = $('form[name=digital-subscription-select]', this.el).serialize();
        this.populateFancyboxPost('/products/digital/select', qs);
        return false; 
    },

    submitLoginForm: function() {
        var login_form = $('form[name=login]', this.el);
        if (login_form.attr('action').substr(0, 5) == 'https') {
            window.location.port = 443;
        }
        var qs = login_form.serialize();
        qs += '&redirect_params[nolayout]=1';
        this.populateFancyboxPost('/profile/login/', qs);
        return false; 
    },

    resetPasswordRequestForm: function() {
        this.populateFancyboxGet('/profile/reset-password-request/');
        return false; 
    },

    submitResetPasswordRequestForm: function() {
        var rpr_form = $('form[name=reset-password-request]', this.el);
        if (rpr_form.attr('action').substr(0, 5) == 'https') {
            window.location.port = 443;
        }
        var qs = rpr_form.serialize();
        this.populateFancyboxPost('/profile/reset-password-request', qs);
        return false;
    }

});

