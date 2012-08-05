/**
 * Global view
 * 
 * For events that may occur on *any* page of the site. This is not a dumping
 * ground for random bits of code!
 * 
 */
Pet.GlobalView = Pet.View.extend({
    
    el: $('body'),

    events: {
    },
    
    initialize: function() {
        $('input[type=text]').attr('autocomplete', 'off');
    }

});

