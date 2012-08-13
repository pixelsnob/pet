/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    el: $('body'),

    error_msg: '<p class="error">An error has ocurred</p>',
    
    spinner: null,

    events: {
    },

    initialize: function() {
        this.replaceGradLinks();
        $('input[type=text]').attr('autocomplete', 'off');
    },
    
    /**
     * Replaces links with gradient inputs, for a consistent look
     * 
     */
    replaceGradLinks: function(el, no_click) {
        el = (typeof el == 'object' && el !== null ? el : this.el); 
        var sel = 'a.button-grad, a.button-grad-yellow, a.button-grad-blue',
            links = $(sel, el);
        links.each(function() {
            var link = $(this),
                input = $('<input type="submit">')
                    .attr({ value: link.text() })
                    .addClass(link.attr('class'))
                    .width(link.width())
                    .css('visibility', 'visible')
                    .data('href', link.attr('href'));
            var no_click = link.hasClass('no-click');
            link.replaceWith(input);
            if (!no_click && input.parents('#nolayout').length == 0) {
                input.click(function() {
                    window.location.href = link.attr('href');
                    return false;
                });
            }
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
            url: url,
            type: (post_data ? 'post' : 'get'),
            async: false,
            success: function(data) {
                $('.fancybox-inner').html(data);
                cb();
                $.fancybox.update();
                obj.replaceGradLinks();
            },
            error: function() {
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
        var obj = this;
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
                obj.replaceGradLinks();
            }
        }, opts);
        $.fancybox(opts);
    },

    /**
     * Creates a fancybox popup and populates it using a link's href
     * 
     */
    showFancyboxFromLink: function(el) {
        this.showFancybox({ href: $(el.target).attr('href') });
        return false;
    },
    
    /**
     * Populates existing fancybox using a link's href
     * 
     */
    populateFancyboxFromLink: function(el) {
        this.populateFancybox($(el.target).attr('href'));
        return false;
    },

    closeFancybox: function() {
        $.fancybox().close();
        return false;
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
