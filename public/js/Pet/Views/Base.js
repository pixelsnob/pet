/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    error_msg: '<p class="error">An error has ocurred</p>',
    
    /**
     * Populates an existing fancybox
     * 
     */
    populateFancybox: function(url, post_data, cb) {
        url += '?nolayout';
        cb = (typeof cb == 'function' ? cb : function() {});
        var obj = this,
        ajax_params = {
            'url': url,
            'type': 'post',
            'async': false,
            'success': function(data) {
                $('.fancybox-inner').html(data);
                cb();
                $.fancybox.update();
            },
            'error': function() {
                $('.fancybox-inner').html(obj.error_msg);
            }
        };
        if (post_data && typeof post_data == 'string') {
            ajax_params.data = post_data;
        }
        $.ajax(ajax_params);
    },

    /**
     * Creates a new fancybox
     * 
     */
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
    },
    
    /**
     * Smooth scroll
     * 
     */
    scrollTo: function(sel, callback) {
        callback = (typeof callback == 'function' ? callback : function() {});
        var el_top = this._$(sel).position().top;
        var c = 0;
        this._$('body, html').animate({
            scrollTop: el_top
        }, 450, function() {
            // Make sure this only runs once
            if (c) {
                callback();
                return;
            }
            c++;
        });
    },

    addFormElementMessages: function(el, msg, type) {
        msg = (typeof msg == 'string' ? [ msg ] : msg);
        type = (type ? type : 'success');
        $(el).parent().find('.errors, .success').remove();
        var ul = $('<ul>').addClass(type);
        for (var m in msg) {
            ul.append($('<li>').text(msg[m]));
        }
        $(el).parent().append(ul);
    }

});
