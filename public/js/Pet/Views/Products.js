/**
 * Products view
 * 
 */
Pet.ProductsView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=subscription-select-term] .submit input':
            'submitSelectTermForm'
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
        this.populateFancyboxPost('/products/subscription/term', qs);
        return false; 
    }

});

