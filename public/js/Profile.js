
var AuthModel = Backbone.Model.extend({
    is_authenticated: null,
    url: 'profile/is-authenticated'
});

var ProfileView = Backbone.View.extend({

    el: $('body'),
    
    events: {
        'click .change-password a': 'changePassword'
    },
    
    initialize: function(){
        _.bindAll(this, 'changePassword'); 
    },

    changePassword: function() {
        var auth = new AuthModel;
        auth.fetch();
        auth.on('change', function(model, t) {
            if (model.get('is_authenticated')) {
                $.fancybox({
                    href: 'profile/change-password/nolayout/1',
                    type: 'iframe',
                    scrolling: 'no'
                });
            } else {
                alert('You have been logged out.');
                window.location.href = '/profile/login';
            }
        });
        return false;
    }

});

var profile = new ProfileView;
