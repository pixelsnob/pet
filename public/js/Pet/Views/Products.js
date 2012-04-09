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
    },
    
    initialize: function(){
        var sel = '.subscription-zones a, .digital-subscription a';
        $(sel, this.el).fancybox(this.getFancyboxOpts());
        $('.renew a', this.el).fancybox(this.getFancyboxOpts({
            afterClose: function() {
                window.location.href = window.location.href;
            }
        }));
        sel += ', .renew a';
        $(sel, this.el).each(function() {
            var href = $(this).attr('href');
            $(this).attr('href', href + '?nolayout');
        });
        this.addContinueShoppingButton();
    },
    
    submitSubscriptionTermSelectForm: function() {
        var qs = $('form[name=subscription-select-term]', this.el).serialize();
        this.populateFancyboxPost('/products/subscription/term', qs);
        //$('#cart .submit', this.el).hide();
        return false; 
    },

    submitDigitalSelectForm: function() {
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
        qs += '&redirect_to=products_subscription_select_term' +
            '&redirect_params[renewal]=1&redirect_params[nolayout]=1';
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
    },

    addContinueShoppingButton: function() {
    }

});

