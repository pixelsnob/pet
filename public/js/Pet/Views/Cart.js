/**
 * Cart view
 * 
 */
Pet.CartView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=cart] .submit input': 'update',
        'click form[name=cart] .remove': 'removeProduct'
    },
    
    initialize: function(){
    },
    
    update: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs);
        return false; 
    },

    removeProduct: function(el) {
        this.populateFancyboxGet($(el.target).attr('href'));
        return false;
    }

});

