/**
 * A container to attach Models, Views, etc.
 * 
 */
var Pet = {
    
    loadView: function(view_name) {
        try {
            return new Pet[view_name + 'View'];
        } catch (e) {
            alert('An error has occurred');
        }
    }
};

