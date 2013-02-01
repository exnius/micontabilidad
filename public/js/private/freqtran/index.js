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
            } else if ($(event.target).hasClass("add-freqtransaction")){
                var nAddFrag = document.createDocumentFragment();
                nAddFrag = ($("#create-freqtran-form").html());
                var $div = $("<div>").append(nAddFrag);
                var $el = $(event.target);
                $.fancybox({
                    'content' : $div,
                    'onStart' : onCreateFreqTranStart($div),
                    'onComplete' : function(){
                        onCreateFreqTranComplete($div, $el);
                    },
                    'onClosed' : function(){
                        $(this.form).find(".hasDatepicker").removeClass("hasDatepicker");
                    }
                });
            }
    });
});

function onCreateFreqTranStart($div){
    $div.find("select[name='frequency_days']").change(function(){
    if(this.value == 0){
        $div.find("input[name='precise_frequency_days']").removeAttr("disabled");
        } else {
            $div.find("input[name='precise_frequency_days']").attr("disabled", true);
        }
    });
    var currentDate = $div.find(".js-time").html();
    $div.find("input[name='date']")
    .val(Contabilidad.toDate(currentDate))
    .datepicker({defaultDate: currentDate,
                 dateFormat: "dd/mm/yy"
     });
}

function onCreateFreqTranComplete($div, $el){
    $div.find("form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input,select").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            data["date"] = $(this).find("input[name='date']").datepicker("getDate").getTime()/1000;
            data = {"name" : data["name"], "date" : data["date"], "value" : data["value"], 
                "id_category_type" : data["id_category_type"], "frequency_time" : data["frequency_time"],
                "precise_frequency_days" : data["precise_frequency_days"],
                "frequency_days" : data["frequency_days"], "id": data["id"]};
            if ($el.attr("id") == "add-income"){
                data.id_transaction_type = 1;
            } else {
                data.id_transaction_type = 2;
            }
            data.is_frequent = 1;
            if(data.frequency_days == 0) {
                data.frequency_days = data.precise_frequency_days;
            } 
            data.value = data.value.replace(/\./g, '').replace(/,/g, '.');
            Contabilidad.getEndPoint({async : true, success: function(resp){
                displaySavedFreqtran(resp);
                $.fancybox.close();
            }}).saveFreqTransaction(data);
        }
        return false;
    });
}

function displaySavedFreqtran(resp)
{
    $(resp.transaction).each(function(){
        var tran = resp.transaction;
        if (tran.frequency_time == 0){
            tran.frequency_time = Contabilidad.tr("Indefinido");
        } else {
            tran.frequency_time = tran.frequency_time;
        }
        if (tran.frequency_days == -1){
            tran.frequency_days = Contabilidad.tr("Mensual");
        } else if (tran.frequency_days == -2) {
            tran.frequency_days = Contabilidad.tr("Quincenal");
        } else if (tran.frequency_days == 7) {
            tran.frequency_days = Contabilidad.tr("Semanal");
        } else if (tran.frequency_days == 1) {
            tran.frequency_days = Contabilidad.tr("Diario");
        } else {
            tran.frequency_days = tran.frequency_days;
        }
        var id_currency = Contabilidad.user.id_currency;
        tran.value = Contabilidad.currencyValue(tran.value, id_currency);
        var output = Mustache.render($("#freqtran-row-tpl").html(), tran);
        $("#transactions-container").prepend(output);
    });
    
    //remove transactions
    if(resp.deleted_transactions){
        $(resp.deleted_transactions).each(function(i){
            var id = resp.deleted_transactions[i];
            $("#transaction-" + id).remove();
            delete Contabilidad.transactions[id];
        });
    }
}