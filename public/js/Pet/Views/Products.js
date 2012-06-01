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
        'click .subscription-options a, #gift-subscriptions .digital a':
            'openSubscriptionSelectPopup',
        'click .renew': 'openRenewPopup',
        'click #digital .buy': 'openDigitalSelectPopup',
        'click .forgot-password a': 'openForgotPasswordPopup'

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
            href: $(el.target).attr('href')
        });
        return false;
    },

    openForgotPasswordPopup: function(el) {
        this.populateFancybox($(el.target).attr('href'));
        return false;
    },

    submitSubscriptionTermSelectForm: function() {
        var obj = this;
        var qs = $('form[name=subscription-select-term]', this.el).serialize();
        this.populateFancybox('/products/subscription/term', qs);
        return false; 
    },

    submitDigitalSelectForm: function() {
        var obj = this;
        var qs = $('form[name=digital-subscription-select]', this.el).serialize();
        this.populateFancybox('/products/digital/select', qs);
        return false; 
    },

    submitLoginForm: function() {
        var login_form = $('form[name=login]', this.el);
        var qs = login_form.serialize();
        qs += '&redirect_params[nolayout]=1';
        this.populateFancybox('/profile/login/', qs);
        return false; 
    },

    resetPasswordRequestForm: function() {
        this.populateFancybox('/profile/reset-password-request/');
        return false; 
    },

    submitResetPasswordRequestForm: function() {
        var rpr_form = $('form[name=reset-password-request]', this.el);
        var qs = rpr_form.serialize();
        this.populateFancybox('/profile/reset-password-request', qs);
        return false;
    },

    openDigitalSelectPopup: function(el) {
        this.showFancybox({ href: $(el.target).attr('href') });
        return false;
    }

});

