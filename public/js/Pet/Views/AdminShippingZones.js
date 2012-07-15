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
        //'click #product-edit .submit': 'overlayFormSubmit',
        'click #shipping-zones .admin-table .delete': 'openDeleteDialogPopup'
        //'click #delete-product-dialog #submit': 'deleteProduct',
        //'click #delete-product-dialog #cancel': 'closeDeleteDialog',
        //'click #delete-product #close': 'closeDeleteDialogAndUpdateList'
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
            //$('#search-form').get(0).submit();
        }
        return false;
    },


    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:first').attr('href');
        window.location.href = href;
        return true;
    }

});

