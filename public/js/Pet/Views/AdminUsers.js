/**
 * Base admin view
 * 
 */
Pet.AdminUsersView = Pet.AdminView.extend({
    
    el: $('body'),

    xhr: [], // An array of Ajax XHR objects
    
    events: {
        'click #users .admin-table td, #user-detail .admin-table td': 'adminTableRowClick',
        'hover #users .admin-table td, #user-detail .admin-table td': 'adminTableRowHover',
        'click #change_password': 'togglePasswordFields',
        'click #user-edit .submit': 'overlayFormSubmit',
        'click #user-detail .add-user-note': 'openAddUserNoteDialog',
        'click #user-note-edit #submit': 'addUserNote'
        //'click #delete-shipping-zone-dialog #cancel': 'closeDeleteDialog',
    },
    
    initialize: function() {
        this.events = _.extend({}, Pet.AdminView.prototype.events, this.events)
        if ($('form[name=user_edit]').length) {
            if ($('#change_password:checked').length) {
                $('.form dd.pw, .form dt.pw').show();
            } else {
                $('.form dd.pw, .form dt.pw').hide();
            }
        }
        Pet.AdminView.prototype.initialize.call(this);
    },

    togglePasswordFields: function(el) {
        if (el.target.checked) {
            $('.form dd.pw, .form dt.pw').show();
        } else {
            $('.form dd.pw, .form dt.pw').hide();
        }
    },

    openAddUserNoteDialog: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href')
        });
        return false;
    },

    addUserNote: function(el) {
        var qs = $('form[name=user_note_edit]', this.el).serialize();
        this.populateFancybox('/admin/users/add-note/', qs);
        return false;
    },

    closeAddUserNoteDialogUpdateList: function(el) {
        $.fancybox.close();
        var delete_status = $('input[name=status]').val();
        if (delete_status == '1') {
            window.location.href = window.location.href;
        }
        return false;
    }


});

