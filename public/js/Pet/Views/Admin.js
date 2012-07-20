/**
 * Base admin view
 * 
 */
Pet.AdminView = Pet.View.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #billing-to-shipping': 'copyBillingToShipping',
        'click .datepicker': 'openDatepicker'
    },
    
    initialize: function() {
        if ($('form').length) {
            $('form:first').find(
                ':input:first:not(.hasDatepicker):not(.no-focus)'
            ).focus();
        }
    },

    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:last').attr('href');
        window.location.href = href;
        return true;
    },

    adminTableRowHover: function(el) {
        $(el.target).parent().parent().find('td').removeClass('hover');
        if (el.type == 'mouseenter') {
            $(el.target).parent().find('td').addClass('hover');
        } else {
            $(el.target).parent().find('td').removeClass('hover');
        }
    },

    copyBillingToShipping: function() {
        $('.billing input, .billing select').each(function() {
            var suffix = $(this).attr('id').replace(/billing_/, '');
            $('#shipping_' + suffix).val($(this).val());
        });
        return false;
    },
    
    overlayFormSubmit: function() {
        this.showSpinnerOverlay(function() {
            $('form').get(0).submit();
        }); 
        return false;
    },

    openDatepicker: function(el) {
        var opts = { 
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            maxDate: new Date,
            onChangeMonthYear: function(year, month, inst) {
                var first_of_month = new Date(year, month - 1, 1);
                $(this).val($.datepicker.formatDate('yy-mm-dd',
                    first_of_month));
                $(this).datepicker('setDate', first_of_month);
            },
            showOn: 'focus'
        };
        if ($(el.target).hasClass('datepicker-no-max')) {
            opts.maxDate = null;
        } else if ($(el.target).hasClass('datepicker-min-today')) {
            opts.minDate = new Date;
            opts.maxDate = null;
        }
        $(el.target).datepicker(opts).focus();
    }

});

