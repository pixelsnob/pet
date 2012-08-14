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
        'click #nolayout #user-note-edit #submit': 'addUserNote',
        'click #nolayout #user-note-edit #cancel': 'closeAddUserNoteDialog',
        'click #nolayout #add-note-success #close': 'closeAddUserNoteDialog',
        'click #user-detail .user-notes .admin-table .delete': 'openDeleteUserNoteDialog',
        'click #nolayout #delete-user-note-dialog #submit': 'deleteUserNote',
        'click #nolayout #delete-user-note-dialog #cancel': 'closeDeleteUserNoteDialog',
        'click #nolayout #delete-user-note #return': 'closeDeleteUserNoteDialog',

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
    
    adminTableRowClick: function(el) {
        var href = $(el.target).parent().find('a:first').attr('href');
        window.location.href = href;
        return true;
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
        console.log('a');
        return false;
    },

    openDeleteUserNoteDialog: function(el) {
        this.showFancybox({
            href: $(el.target).attr('href')
        });
        return false;
    },

    closeAddUserNoteDialog: function(el) {
        $.fancybox.close();
        if ($(el.target).attr('id') == 'close') {
            window.location.href = window.location.href;
        }
        return false;
    },

    closeDeleteUserNoteDialog: function(el) {
        $.fancybox.close();
        if ($(el.target).attr('id') == 'return') {
            window.location.href = window.location.href;
        }
        return false;
    },

    deleteUserNote: function(el) {
        $(el.target).parent().find('input[type=submit]').attr('disabled', true);
        var params = {
            id: $('input[name=id]', this.el).val(),
            submit: 1
        };
        this.populateFancybox('/admin/users/delete-note-dialog/', $.param(params));
        return false;
    }


});

