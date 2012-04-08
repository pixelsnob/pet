/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    error_msg: '<p class="error">An error has ocurred</p>',

    populateFancyboxGet: function(url) {
        url += '?nolayout';
        $.ajax({
            'url': url,
            'type': 'get',
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
            },
            'error': $('.fancybox-inner').html(this.error_msg)
        });
    },

    populateFancyboxPost: function(url, post_data) {
        url += '?nolayout';
        $.ajax({
            'url': url,
            'type': 'post',
            'data': post_data,
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
            },
            'error': $('.fancybox-inner').html(this.error_msg)
        });
    },

    getFancyboxOpts: function(opts) {
        return $.extend({
            type: 'ajax',
            scrolling: 'no',
            width: 550,
            height: 350,
            autoSize: false
        }, opts);
    }

});
