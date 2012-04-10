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
    },
    
    initialize: function(){
    },
    
    update: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs);
        this.configureCart();
        return false; 
    },

    removeProduct: function(el) {
        this.populateFancyboxGet($(el.target).attr('href'));
        this.configureCart();
        return false;
    },

    selectQty: function(el) {
        el.target.select();
        return false;
    },

    configureCart: function() {
        var obj = this;
        $('#cart .submit', this.el).hide();
        $('#cart .item', this.el).each(function() {
            var qty = $(this).find('input');
            if (qty.hasClass('readonly')) {
                return true;
            }
            $(this).find('.links').prepend(
                $('<a>').attr('href', '#').text('Update').on(
                    'click', function() {
                        obj.update();
                    }
                )
            );
        });
    }

});

