/**
 * Products view
 * 
 */
Pet.ProductsView = Backbone.View.extend({
    
    el: $('body'),
    
    events: {
        'click .terms a': 'openTermSelectPopup'
    },
    
    initialize: function(){
        
    },
    
    /**
     * Shows the subscription term select form in a lightbox
     * 
     */
    openTermSelectPopup: function(el) {
        var href = $(el.target, this.el).attr('href');
        $.fancybox({
            href: href + '?nolayout/1',
            type: 'iframe',
            scrolling: 'no'
        });
        return false;
    }

});

