/**
 * Admin promos view
 * 
 */
Pet.AdminPromosView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #promos .admin-table td': 'adminTableRowClick',
        'hover #promos .admin-table td': 'adminTableRowHover',
        'click #promo-edit .submit': 'overlayFormSubmit',
        'click #promos .admin-table .delete': 'openDeleteDialogPopup',
        'click #delete-promo-dialog #submit': 'deleteShippingZone',
        'click #delete-promo-dialog #cancel': 'closeDeleteDialog',
        'click #delete-promo-dialog #close': 'closeDeleteDialogAndUpdateList',
        'click #promo-edit .delete-banner': 'deleteBanner'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
    },

    openDeleteDialogPopup: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href')
        });
        return false;
    },
    
    deleteShippingZone: function(el) {
        var id = $('input[name=id]', this.el).val();
        this.populateFancybox('/admin/promos/delete/id/' + id);
        return false;
    },

    closeDeleteDialog: function(el) {
        $.fancybox.close();
        return false;
    },

    closeDeleteDialogAndUpdateList: function(el) {
        $.fancybox.close();
        var delete_status = $('input[name=status]').val();
        if (delete_status == '1') {
            window.location.href = window.location.href;
        }
        return false;
    },

    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:first').attr('href');
        window.location.href = href;
        return true;
    },

    deleteBanner: function(el) {
        $('#tmp_banner', this.el).val('');
        $('#banner-image img', this.el).fadeTo(100, .2);
        var msg = 'Image marked for deletion: save form to make change permanent';
        $('#banner-image p').append($('<p>').text(msg).addClass('alert'));
        $('#delete_banner', this.el).val(1);
        $('a.delete-banner').remove();
        return false;
    }

});

