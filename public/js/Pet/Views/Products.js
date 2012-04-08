/**
 * Products view
 * 
 */
Pet.ProductsView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click form[name=subscription-select-term] .submit input':
            'submitSubscriptionTermSelectForm',
        'click form[name=digital-subscription-select] .submit input':
            'submitDigitalSelectForm'
    },
    
    initialize: function(){
        $('.subscription-zones a', this.el).each(function() {
            var href = $(this).attr('href');
            $(this).attr('href', href + '?nolayout');
        });
        $('.subscription-zones a', this.el).fancybox(this.getFancyboxOpts());
        $('.digital-subscription a', this.el).each(function() {
            var href = $(this).attr('href');
            $(this).attr('href', href + '?nolayout');
        });
        $('.digital-subscription a', this.el).fancybox(this.getFancyboxOpts());
    },
    
    submitSubscriptionTermSelectForm: function() {
        var qs = $('form[name=subscription-select-term]', this.el).serialize();
        this.populateFancyboxPost('/products/subscription/term', qs);
        return false; 
    },

    submitDigitalSelectForm: function() {
        var qs = $('form[name=digital-subscription-select]', this.el).serialize();
        this.populateFancyboxPost('/products/digital/select', qs);
        return false; 
    }

});

