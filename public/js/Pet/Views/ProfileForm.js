/**
 * Profile form view
 * 
 */
Pet.ProfileFormView = Backbone.View.extend({
    
    el: $('body'),
    
    events: {
        'click .change-password a': 'changePassword'
    },
    
    initialize: function(){
        
    },
    
    /**
     * Shows the "change password" form in a lightbox
     * 
     */
    changePassword: function() {
        var auth = new Pet.AuthModel;
        auth.fetch();
        auth.on('change', function(model) {
            if (model.get('is_authenticated')) {
                $.fancybox({
                    href: '/profile/change-password/nolayout/1',
                    type: 'ajax',
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

