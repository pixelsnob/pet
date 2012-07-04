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
        //'click .datepicker': 'openDatepicker'
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
    }
});

