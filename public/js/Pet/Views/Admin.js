/**
 * Base admin view
 * 
 */
Pet.AdminView = Pet.View.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
    },
    
    initialize: function() {
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            maxDate: (new Date)
        });
    }
});

