/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    error_msg: '<p class="error">An error has ocurred</p>',

    populateFancyboxGet: function(url) {
        url += '?nolayout';
        var obj = this;
        $.ajax({
            'url': url,
            'type': 'get',
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
                $.fancybox.update();
            },
            'error': function() {
                $('.fancybox-inner').html(obj.error_msg);
            }
        });
    },

    populateFancyboxPost: function(url, post_data) {
        url += '?nolayout';
        var obj = this;
        $.ajax({
            'url': url,
            'type': 'post',
            'data': post_data,
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
                $.fancybox.update();
            },
            'error': function() {
                $('.fancybox-inner').html(obj.error_msg);
            }
        });
    },

    getFancyboxOpts: function(opts) {
        return $.extend({
            type: 'ajax',
            scrolling: 'no',
            minWidth: 650,
            minHeight: 300,
            fitToView: true,
            autoSize: true,
            scrolling: true,
            afterLoad: function() {
                $.fancybox.update();
            }
        }, opts);
    },

    showFancybox: function(opts) {
        if (opts.href) {
            opts.href += '?nolayout';
        }
        opts = $.extend({
            type: 'ajax',
            scrolling: 'no',
            minWidth: 650,
            minHeight: 300,
            fitToView: true,
            autoSize: true,
            scrolling: true,
            afterLoad: function() {
                $.fancybox.update();
            }
        }, opts);
        $.fancybox(opts);
    }

});
