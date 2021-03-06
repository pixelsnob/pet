/**
 * Profile form view
 * 
 */
Pet.ProfileFormView = Pet.View.extend({
    
    el: $('body'),
    
    events: {
        'click .change-password a': 'changePassword',
        'click form[name=change-password] .submit input':
            'submitChangePasswordForm'
    },
    
    initialize: function(){
        this.events = $.extend({}, Pet.View.prototype.events, this.events)
        Pet.View.prototype.initialize.call(this);
    },

    /**
     * Shows the "change password" form in a lightbox
     * 
     */
    changePassword: function() {
        var auth = new Pet.AuthModel;
        var obj = this;
        auth.fetch();
        auth.on('change', function(model) {
            if (model.get('is_authenticated')) {
                obj.showFancybox({
                    href: '/profile/change-password/nolayout/1'
                });
            } else {
                alert('You have been logged out.');
                window.location.href = '/profile/login';
            }
        });
        return false;
    },

    submitChangePasswordForm: function() {
        var qs = $('form[name=change-password]', this.el).serialize();
        this.populateFancybox('/profile/change-password', qs);
        return false;
    }

});

