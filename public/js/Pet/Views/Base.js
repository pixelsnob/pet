/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    error_msg: '<p class="error">An error has ocurred</p>',
    
    spinner: null,

    events: {
        //'click a.button-grad': 'linkSubmitForm'
    },

    initialize: function() {
        this.replaceGradLinks();
    },
    
    /**
     * Replaces links with gradient inputs, for a consistent look
     * 
     */
    replaceGradLinks: function() {
        var links = $('a.button-grad, a.button-grad-yellow, a.button-grad-blue');
        links.each(function() {
            var link = $(this);
            link.replaceWith(
                $('<input type="submit">').attr({ value: link.text() })
                    .addClass(link.attr('class'))
                    .width(link.width())
                    .css('visibility', 'visible')
                    .click(function() {
                        window.location.href = link.attr('href');
                        return false;
                    })
            );
        });
    },

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
            'type': (post_data ? 'post' : 'get'),
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
        sel = $(sel);
        callback = (typeof callback == 'function' ? callback : function() {});
        var el_top = sel.position().top;
        var c = 0;
        $('body, html').animate({
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
    },
    
    
    /**
     * Creates an overlay with a spinner image
     * 
     */
    showSpinnerOverlay: function(cb) {
        var spinner = $('<img>').attr('src', '/images/ajax-loader.gif'),
            obj = this;
        spinner.on('load', function() {
            obj.spinner = $('<div>')
                .attr('id', 'spinner-box')
                .hide()
                .append(spinner).appendTo('body');
            obj.spinner.overlay({
                mask: {
                    color: '#000',
                    loadSpeed: 200,
                    opacity: 0.4
                },
                load: false,
                top: '30%',
                closeOnClick: false,
                closeOnEsc: false,
                fixed: true,
                onClose: function() {
                    $('#spinner-box').hide();
                }
            });
            obj.spinner.overlay().load();
            if (typeof cb == 'function') {
                cb();
            }
        });
    },

    hideSpinnerOverlay: function() {
        this.spinner.overlay().close();
    }

});
