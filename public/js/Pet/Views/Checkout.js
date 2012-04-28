/**
 * Checkout view
 * 
 */
Pet.CheckoutView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click #use_shipping': 'toggleShippingFields',
        'click input[name=payment_method]': 'toggleCCFields',
        'change input[name=promo_code]': 'savePromoCode',
        'change input[name!=promo_code], .billing select, .shipping select, .profile select': 'saveForm'
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
        promo.save({ code: el.val() }, {
            success: function(model, response) {
                var type = 'errors';
                if (model.get('success') === 1) {
                    type = 'success';
                    // Get updated total after applying promo
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
    },

    saveForm: function(el) {
        el = $(el.target);
        /*if (!$.trim(el.val()).length) {
            el.parent().find('.errors').remove();
            return;
        }*/
        var obj = this;
        Backbone.emulateJSON = true;
        var checkout = new Pet.CheckoutModel;
        checkout.save($('form[name=checkout]', this.el).serializeArray(), {
            success: function(model, response) {
                // Remove existing errors
                el.parent().find('.errors').remove();
                var messages = model.get('messages'); 
                // Step through messages, see if current element's name is in
                // this structure
                for (var i in messages) {
                    for (var j in messages[i]) {
                        if (el.attr('name') == j) {
                            var msg = '';
                            for (var k in messages[i][j]) {
                                msg = messages[i][j][k];
                            }
                            if (msg) {
                                // Display error
                                obj.addFormElementMessage(el, msg, 'errors');
                            }
                        }
                    }
                }
            }
        });
    }
    

});

