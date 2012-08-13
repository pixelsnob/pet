/**
 * Products view
 * 
 */
Pet.ProductsView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'mousedown #products-index .subscribe-renew a': 'subButtonDown',
        'mouseup #products-index .subscribe-renew a': 'subButtonUp',
        'mouseover #products-index .subscribe-renew a': 'subButtonOver',
        'mouseout #products-index .subscribe-renew a': 'subButtonOut',
        'click #products-index a.renew': 'showFancyboxFromLink',
        'click #products-index .dvds .learn-more': 'openPhysicalDetailPopup',

        'mousedown #products-special .offers a': 'subButtonDown',
        'mouseup #products-special .offers a': 'subButtonUp',
        'mouseover #products-special .offers a': 'subButtonOver',
        'mouseout #products-special .offers a': 'subButtonOut',
        'click #products-special .offers a': 'showFancyboxFromLink',

        'click #nolayout #login-form #login-submit': 'submitLoginForm',
        'click #nolayout #login-form .forgot-password': 'populateFancyboxFromLink',
        'click #nolayout #reset-password-request .submit': 'submitResetPasswordRequestForm',

        'click #nolayout .cart-add': 'addProductToCart',
        'click #nolayout .continue-shopping': 'closeFancybox',

        'click #nolayout #products-subscription-options .submit input': 'submitSubscriptionOptionsForm',

        'click #nolayout #subscription-zones .zones input': 'showSubscriptionOptions',
        'click #gifts .buttons-list input': 'showGiftOptions'

    },
    
    initialize: function(){
        Pet.View.prototype.initialize.call(this);
        Pet.loadView('Cart'); 
    },

    subButtonDown: function(el) {
        $(el.target).fadeTo(1, 0.5);
    },

    subButtonUp: function(el) {
        $(el.target).fadeTo(1, 0.8);
    },

    subButtonOver: function(el) {
        this.button_timeout = window.setTimeout(function() {
            $(el.target).fadeTo(200, 0.8);
        }, 100);
    },

    subButtonOut: function(el) {
        window.clearTimeout(this.button_timeout);
        $(el.target).fadeTo(130, 1);
    },

    submitLoginForm: function() {
        var login_form = $('form[name=login]', this.el),
            qs         = login_form.serialize() + '&redirect_params[nolayout]=1',
            obj        = this;
        this.populateFancybox('/products/renewal-options', qs);
        return false; 
    },

    submitResetPasswordRequestForm: function() {
        var form = $('form[name=reset-password-request]', this.el);
        var qs = form.serialize();
        this.populateFancybox('/profile/reset-password-request', qs);
        return false;
    },
    
    addProductToCart: function(el) {
        this.populateFancybox($(el.target).data('href')); 
        return false;
    },

    submitSubscriptionOptionsForm: function() {
        var form = $('form[name=subscription-options]', this.el),
            qs = form.serialize(),
            url;
        if ($.trim($('input[name=is_renewal]').val()).length) {
            url = '/products/renewal-options';
        } else {
            url = '/products/subscription-options';
        }
        this.populateFancybox(url, qs);
        return false;
    },

    openPhysicalDetailPopup: function(el) {
        this.showFancybox({ href: $(el.target).data('href') });
        return false;
    },

    showSubscriptionOptions: function(el) {
        this.populateFancybox($(el.target).data('href')); 
        return false;
    },

    showGiftOptions: function(el) {
        this.showFancybox({ href: $(el.target).data('href') }); 
        return false;
    }

});

