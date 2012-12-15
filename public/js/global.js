var Contabilidad = {
    endPoint : function(options){
        options = jQuery.extend({url: '/jsonrpc'}, options);
        return jQuery.Zend.jsonrpc(options);
    },
    private_home : BASE_URL + "/private/index/home"
};

//translate function
Contabilidad.tr = function(string){
    return string;
}

$(document).ready(function(){
    
});