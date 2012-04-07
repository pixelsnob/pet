/**
 * Base view class
 * 
 */
Pet.View = Backbone.View.extend({
    
    populateFancyboxGet: function(url) {
        url += '?nolayout';
        $.ajax({
            'url': url,
            'type': 'get',
            'success': function(data) {
                $('.fancybox-inner').html(data);
            }
        });
    },

    populateFancyboxPost: function(url, post_data) {
        url += '?nolayout';
        $.ajax({
            'url': url,
            'type': 'post',
            'data': post_data,
            'success': function(data) {
                $('.fancybox-inner').html(data);
            }
        });
    }

});
