/**
 * Checkout confirmation view
 * 
 */
Pet.ConfirmationView = Pet.View.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
    },
    
    initialize: function() {
        Pet.View.prototype.initialize.call(this);
    }

});

