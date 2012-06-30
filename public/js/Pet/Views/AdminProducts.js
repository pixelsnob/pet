/**
 * Admin products view
 * 
 */
Pet.AdminProductsView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #products .admin-table td': 'adminTableRowClick',
        'hover #products .admin-table td': 'adminTableRowHover'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
    }
});

