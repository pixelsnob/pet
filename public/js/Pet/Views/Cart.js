/**
 * Cart view
 * 
 */
Pet.CartView = Pet.View.extend({
    
    el: $('body'),
    
    model: new Pet.CartModel,

    events: {
        //'click .add-to-cart': 'openCartPopup',
        'click #nolayout #cart .remove': 'populateFancyboxFromLink',
        'click #nolayout #cart .update': 'update',
        'mousedown #nolayout #cart .remove, #cart .update': 'fadeOutItem',
        'mouseup #nolayout #cart .update, #cart .remove': 'fadeInItem',
        'click #nolayout #cart .checkout input': 'goToCheckout',
        'click #nolayout #cart .continue-shopping input': 'continueShopping',
        'mouseup #nolayout #cart .items input': 'qtySelectMouseup',
        'focus #nolayout #cart .items input': 'qtySelectFocus'
    },
    
    initialize: function() {
    },
    
    /*openCartPopup: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href')
        });
        return false;
    },*/

    update: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancybox('/cart', qs);
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
        obj.populateFancybox($(el.target).attr('href'));
        return false;
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

