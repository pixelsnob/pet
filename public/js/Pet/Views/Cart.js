/**
 * Cart view
 * 
 */
Pet.CartView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=cart] .submit input': 'update',
        'click form[name=cart] .remove': 'removeProduct',
        'focus #cart .items input': 'selectQty'
        //'change #cart .items input': 'update'
    },
    
    initialize: function(){
    },
    
    update: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs);
        //$('#cart .submit', this.el).hide();
        return false; 
    },

    removeProduct: function(el) {
        this.populateFancyboxGet($(el.target).attr('href'));
        return false;
    },

    selectQty: function(el) {
        el.target.select();
        return false;
    }

});

