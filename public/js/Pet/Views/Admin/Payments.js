/**
 * Admin payments view
 * 
 */
Pet.AdminPaymentsView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #payments .admin-table td': 'adminTableRowClick',
        'hover #payments .admin-table td': 'adminTableRowHover'
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
    }


});

