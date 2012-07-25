/**
 * Admin shipping zones view
 * 
 */
Pet.AdminShippingZonesView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #shipping-zones .admin-table td': 'adminTableRowClick',
        'hover #shipping-zones .admin-table td': 'adminTableRowHover',
        'click #shipping-zone-edit .submit': 'overlayFormSubmit',
        'click #shipping-zones .admin-table .delete': 'openDeleteDialogPopup',
        'click #delete-shipping-zone-dialog #submit': 'deleteShippingZone',
        'click #delete-shipping-zone-dialog #cancel': 'closeDeleteDialog',
        'click #delete-shipping-zone-dialog #close': 'closeDeleteDialogAndUpdateList'
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
        this.populateFancybox('/admin/shipping-zones/delete/id/' + id);
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
    }

});

