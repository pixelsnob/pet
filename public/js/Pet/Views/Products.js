/**
 * Products view
 * 
 */
Pet.ProductsView = Backbone.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=subscription-select-term] .submit input':
            'submitSelectTermForm',
        'click form[name=cart] .submit input': 'updateCart',
        'click form[name=cart] .remove': 'removeProduct'
    },
    
    initialize: function(){
        $('.subscription-zones a', this.el).each(function() {
            var href = $(this).attr('href');
            $(this).attr('href', href + '?nolayout');
        });
        $('.subscription-zones a', this.el).fancybox({
            type: 'ajax',
            scrolling: 'no',
            width: 400,
            height: 300,
            autoSize: false
        });
    },
    
    submitSelectTermForm: function(el) {
        var qs = $('form[name=subscription-select-term]', this.el).serialize();
        $.ajax({
            'url': '/products/subscription/term/?nolayout',
            'type': 'post',
            'data': qs,
            'success': function(data) {
                $('.fancybox-inner').html(data);
            }
        });
        return false; 
    },

    updateCart: function() {
        var qs = $('form[name=cart]', this.el).serialize();
        $.ajax({
            'url': '/cart?nolayout',
            'type': 'post',
            'data': qs,
            'success': function(data) {
                $('.fancybox-inner').html(data);
            }
        });
        return false; 
    },

    removeProduct: function(el) {
        $.ajax({
            'url': $(el.target).attr('href'),
            'type': 'get',
            'success': function(data) {
                $('.fancybox-inner').html(data);
            }
        });
        return false;
    }

});

