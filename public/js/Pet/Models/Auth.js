/**
 * Model to determine if user is logged in or not
 * 
 */
Pet.AuthModel = Backbone.Model.extend({
    is_authenticated: null,
    url: '/profile/is-authenticated'
});

