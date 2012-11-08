/**
 * Admin products view
 * 
 */
Pet.AdminProductsView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #products .admin-table td': 'adminTableRowClick',
        'hover #products .admin-table td': 'adminTableRowHover',
        'change #product-edit #product_type_id': 'productTypeIdChange',
        'click #product-edit .submit': 'overlayFormSubmit',
        'click #products .admin-table .delete': 'openDeleteDialogPopup',
        'click #delete-product-dialog #submit': 'deleteProduct',
        'click #delete-product-dialog #cancel': 'closeDeleteDialog',
        'click #delete-product #close': 'closeDeleteDialogAndUpdateList'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
    },

    productTypeIdChange: function(el) {
        var product_type_id = $(el.target);
        product_type_id.attr('disabled', true);
        var qs = $('form[name=product_edit]', this.el).serialize();
        $.ajax({
            type: 'get',
            url: '/admin/products/product-subform',
            data: { product_type_id: product_type_id.val() },
            success: function(data) {
                $('#product-subform').html(data);
                product_type_id.attr('disabled', false);
            }
        });
    },

    openDeleteDialogPopup: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href')
        });
        return false;
    },
    
    deleteProduct: function(el) {
        var id = $('input[name=id]', this.el).val();
        this.populateFancybox('/admin/products/delete/id/' + id);
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
            $('#search-form').get(0).submit();
        }
        return false;
    },


    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:first').attr('href');
        window.location.href = href;
        return true;
    }

});

