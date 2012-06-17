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
        var opts = { 
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            maxDate: (new Date),
            onChangeMonthYear: function(year, month, inst) {
                var first_of_month = new Date(year, month - 1, 1);
                $(this).val($.datepicker.formatDate('yy-mm-dd',
                    first_of_month));
                $(this).datepicker('setDate', first_of_month);
            }
        };
        $('.datepicker').datepicker(opts);
        opts.maxDate = null;
        $('.datepicker-no-max').datepicker(opts);
    }

});

