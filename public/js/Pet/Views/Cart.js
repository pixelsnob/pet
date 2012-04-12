/**
 * Cart view
 * 
 */
Pet.CartView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click #cart .remove': 'removeProduct',
        'mousedown #cart .remove, #cart .update': 'fadeOutItem',
        'mouseup #cart .update, #cart .remove': 'fadeInItem'
    },
    
    initialize: function(){
    },
    
    update: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        this.populateFancyboxPost('/cart', qs);
        this.configureCart();
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
        //$(el.target).parents('li.item').fadeTo(400, .2, function() {
            obj.populateFancyboxGet($(el.target).attr('href'));
            obj.configureCart();
        //});
        return false;
    },

    configureCart: function() {
        var obj = this;
        $('#cart .submit input', this.el).hide();
        // The mouseup is due to a bug: http://code.google.com/p/chromium/issues/detail?id=4505
        $('#cart .items input', this.el).on('mouseup', function(e) {
            e.preventDefault();
        // Make text inside input selected
        }).on('focus', function() {
            this.select();
            return true;
        });
        // Continue shopping button
        $('#cart form').append(
            $('<input>').attr({ type: 'submit', value: 'Continue Shopping' })
                .on('click', function() {
                    $.fancybox.close();
                    return false;
                })
        );
        // Add update links
        $('#cart .item', this.el).each(function() {
            var qty = $(this).find('input');
            // Readonly inputs don't need an update link
            if (qty.hasClass('readonly')) {
                return true;
            }
            $(this).find('.links').prepend(
                $('<li>').append(
                    $('<a>').attr('href', '#').text('Update')
                        .addClass('update')
                        .on(
                            'click', function() {
                                obj.update();
                            }
                        )
                )
            );
        });
    }

});

