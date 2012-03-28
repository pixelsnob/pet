
var ProfileView = Backbone.View.extend({

    el: $('body'),
    
    events: {
        'click .change-password a': 'test'
    },
    
    initialize: function(){
        _.bindAll(this, 'test'); 
    },

    test: function(q, p) {
        $.fancybox({
            href: 'profile/change-password/nolayout/1',
            type: 'iframe',
            scrolling: 'no'
        });
        return false;
    }

});

var profile = new ProfileView;
