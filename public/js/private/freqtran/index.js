$(document).ready(function(){
    $("body").click(function(event){
        if($(event.target).hasClass("delete-freqtransaction")){
            var $parent = $(event.target).parents(".transaction-container");
            var name = $parent.hasClass("js-transaction-income") ? Contabilidad.tr("ingreso") : Contabilidad.tr("egreso");
            var output = Mustache.render($("#delete-popup-tpl").html(), 
                        {message: Contabilidad.tr("Esta a punto de eliminar una transaccion frecuente, esto afectará la \n\
                        creación de futuros balances\n\¿Realmente quieres eliminar esta transacción?", name)});
            $.fancybox({
                'content' : output,
                'onComplete' : function(){
                    $("#delete-popup input[type='button']").click(function(){
                        if($(this).attr("id") == "yes"){
                            var id = $parent.attr("data-id");
                            $parent.remove();
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                            }}).deleteFreqTran(id);
                        }
                        $.fancybox.close();
                    });
                }
            });
            return false;
        }
    });
});