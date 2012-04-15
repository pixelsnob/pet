/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    error_msg: '<p class="error">An error has ocurred</p>',

    populateFancyboxGet: function(url, cb) {
        cb = (typeof cb == 'function' ? cb : function() {});
        url += '?nolayout';
        var obj = this;
        $.ajax({
            'url': url,
            'type': 'get',
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
                cb();
                $.fancybox.update();
            },
            'error': function() {
                $('.fancybox-inner').html(obj.error_msg);
            }
        });
    },

    populateFancyboxPost: function(url, post_data, cb) {
        cb = (typeof cb == 'function' ? cb : function() {});
        url += '?nolayout';
        var obj = this;
        $.ajax({
            'url': url,
            'type': 'post',
            'data': post_data,
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
                cb();
                $.fancybox.update();
            },
            'error': function() {
                $('.fancybox-inner').html(obj.error_msg);
            }
        });
    },

    showFancybox: function(opts, cb) {
        if (opts.href) {
            opts.href += '?nolayout';
        }
        cb = (typeof cb == 'function' ? cb : function() {});
        opts = $.extend({
            type: 'ajax',
            scrolling: 'no',
            minWidth: 680,
            minHeight: 300,
            fitToView: true,
            autoSize: true,
            scrolling: true,
            beforeShow: function() {
                cb();
                $.fancybox.update();
            }
        }, opts);
        $.fancybox(opts);
    }

});
