/**
 * Cart view
 * 
 */
Pet.CartView = Pet.View.extend({
    
    el: $('body'),
    
    model: new Pet.CartModel,

    events: {
        'click .add-to-cart': 'openCartPopup',
        'click #cart .remove': 'removeProduct',
        'click #cart .update': 'update',
        'mousedown #cart .remove, #cart .update': 'fadeOutItem',
        'mouseup #cart .update, #cart .remove': 'fadeInItem',
        'click #cart .checkout input': 'goToCheckout',
        'click #cart .continue-shopping input': 'continueShopping',
        'mouseup #cart .items input': 'qtySelectMouseup',
        'focus #cart .items input': 'qtySelectFocus'
    },
    
    initialize: function(){
    },
    
    openCartPopup: function(el) {
        var obj = this;
        this.showFancybox({
            href: $(el.target).attr('href')
        }, function() {
            obj.configureCart();
        });
        return false;
    },

    update: function() {
        var obj = this;
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs, function() {
            obj.configureCart();
        });
        return false; 
    },

    fadeOutItem: function(el) {
        $(el.target).parents('li.item').css('opacity', 0.5);
    },

    fadeInItem: function(el) {
        $(el.target).parents('li.item').css('opacity', 1);
    },

    removeProduct: function(el) {
        var obj = this;
        obj.populateFancyboxGet($(el.target).attr('href'), function() {
            obj.configureCart();
        });
        return false;
    },

    configureCart: function() {
        //$('#cart .submit input', this.el).hide();
        // The mouseup is due to a bug:
        // http://code.google.com/p/chromium/issues/detail?id=4505
        /*$('#cart .items input', this.el).on('mouseup', function(e) {
            e.preventDefault();
        // Make text inside input selected
        }).on('focus', function() {
            this.select();
            return true;
        });*/
    },
    
    continueShopping: function() {
        $.fancybox.close();
        return false;
    },

    goToCheckout: function() {
        $.fancybox.close();
        window.setTimeout(function() {
            window.location.href = '/checkout';
        }, 400);
        return false;
    },

    qtySelectFocus: function(el) {
        el.target.select();
        return false;
    },

    qtySelectMouseup: function(el) {
        el.preventDefault();
        return false;
    }

});

