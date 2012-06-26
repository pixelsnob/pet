/**
 * Admin orders view
 * 
 */
Pet.AdminOrdersView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        Pet.AdminView.prototype.initialize.call(this);
    }

    /*adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:last').attr('href');
        this.showFancybox({ href: href, width: 880 });
        return false;
    },*/

});

