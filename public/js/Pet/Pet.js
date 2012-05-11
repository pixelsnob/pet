/**
 * A container to attach Models, Views, etc.
 * 
 */
var Pet = {
    
    loadView: function(view_name) {
        if (navigator.userAgent.match(/iPhone|iPad/i)) {
            return;
        }
        try {
            return new Pet[view_name + 'View'];
        } catch (e) {
            alert('An error has occurred');
        }
    }
};

