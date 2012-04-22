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
        });
        return false;
    },

    update: function() {
        var obj = this;
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs);
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
        obj.populateFancyboxGet($(el.target).attr('href'));
        return false;
    },

    continueShopping: function() {
        $.fancybox.close();
        window.setTimeout(function() {
            window.location.href = '/products';
        }, 500);
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

