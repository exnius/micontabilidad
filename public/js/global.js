window.Contabilidad = window.Contabilidad || {};

Contabilidad.getEndPoint =  function(options){
    if(options && options.async){
        if(!this.endAsyncPoint){
            options = jQuery.extend({url: '/jsonrpc', async: true}, options);
            this.endAsyncPoint = jQuery.Zend.jsonrpc(options);
        }
        if(options.success) this.endAsyncPoint.setAsyncSuccess(options.success);
        return this.endAsyncPoint;
    } else {
        if(!this.endPoint){
            options = jQuery.extend({url: '/jsonrpc'}, options);
            this.endPoint = jQuery.Zend.jsonrpc(options);
        }
        return this.endPoint;
    }
};
    
Contabilidad.getURLParameter = function (name) {
    return decodeURI((RegExp("[\\?&#]" + name + '=' + '(.+?)(&|$)').exec(window.location)||[,null])[1]);
};

Contabilidad.private_home = BASE_URL + "/private/index/home";

Contabilidad.htmlDecode = function (input)
{
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
};

//translate function
Contabilidad.tr = function(string){
    var args = [].slice.call(arguments);
    if(!args.length) return "";
    args[0] = Contabilidad.htmlDecode(args[0]); //replace html code
    if(args.length > 1){
        var i=0;
        args[0] = args[0].replace(/%s/g, function (matched, group) {
            // Only disallow undefined values
            i++;
            return (args[i] && typeof(args[i]) !== 'undefined') ? args[i] : matched;
        });
    }
    return args[0];
};

Contabilidad.currencyValue = function(){
    var args = [].slice.call(arguments);
    if(!args.length) return "";
    var value = args[0];
    switch (args[1]){
        case '1':
            value = "$ " + value;
            break;
        default :
            value = "USD " + value;
            break;
    }
    return value;
}

Contabilidad.toDate = function(timestamp){
    var date = new Date((timestamp) * 1000);
    if (date.getDate()<10){
        fday = "0" + date.getDate();
    } else {
        fday = date.getDate();
    }
    if ((date.getMonth() + 1 )<10){
        fmonth = "0" + (date.getMonth() + 1 );
    } else {
        fmonth = (date.getMonth() + 1 );
    }
    return  fday + "/" + fmonth + "/" + date.getFullYear();
}