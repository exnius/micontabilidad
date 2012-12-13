var Contabilidad = {
    endPoint : jQuery.Zend.jsonrpc({url: '/jsonrpc'}),
    private_home : BASE_URL + "/private/index/home"
};

//translate function
Contabilidad.tr = function(string){
    return string;
}

$(document).ready(function(){
    
});