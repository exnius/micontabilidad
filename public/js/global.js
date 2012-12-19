var Contabilidad = {
    getEndPoint : function(options){
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
    },
    private_home : BASE_URL + "/private/index/home"
};

//translate function
Contabilidad.tr = function(string){
    return string;
}