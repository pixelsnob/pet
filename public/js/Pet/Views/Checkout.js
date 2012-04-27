/**
 * Checkout view
 * 
 */
Pet.CheckoutView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click #use_shipping': 'toggleShippingFields',
        'click input[name=payment_method]': 'toggleCCFields',
        'change input[name=promo_code]': 'savePromoCode'
    },
    
    initialize: function(){
    },

    toggleShippingFields: function(el) {
        if ($(el.target).is(':checked')) {
            $('fieldset.shipping', this.el).fadeIn();
        } else {
            $('fieldset.shipping', this.el).fadeOut();
        }
        return true;
    },

    toggleCCFields: function(el) {
        if ($(el.target).val() == 'credit_card') {
            $('.payment .cc', this.el).fadeIn();
        } else {
            $('.payment .cc', this.el).fadeOut();
        }
        return true;
    },

    savePromoCode: function(el) {
        el = $(el.target);
        var obj = this;
        Backbone.emulateJSON = true;
        var promo = new Pet.PromoCodeModel;
        promo.set({ code: el.val() });
        promo.save(null, {
            success: function(model, response) {
                var type = 'errors';
                if (model.get('success') === 1) {
                    type = 'success';
                    var cart = new Pet.CartModel;
                    cart.fetch();
                    cart.on('change', function(model) {
                        var totals = cart.get('totals');
                        if (typeof totals.total == 'number') {
                            var total = '$' + totals.total.toFixed(2);
                            $('.total-value').text(total);
                        }
                    });
                }
                obj.addFormElementMessage(el, model.get('message'), type);
            }
        });
        return true;
    }
    

});

