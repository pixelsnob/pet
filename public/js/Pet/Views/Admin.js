/**
 * Base admin view
 * 
 */
Pet.AdminView = Pet.View.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click .admin-table td': 'adminTableRowClick',
        'hover .admin-table td': 'adminTableRowHover'
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
    },

    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:last').attr('href');
        window.location.href = href;
        return true;
    },

    adminTableRowHover: function(el) {
        if (el.type == 'mouseenter') {
            $(el.target).parent().find('td').addClass('hover');
        } else {
            $(el.target).parent().find('td').removeClass('hover');
        }
    }

});

