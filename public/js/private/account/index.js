$(document).ready(function(){
    
    if(!$("body").data("transaction-names")){
        var transactionNames = [];
        $(".js-transaction-name").each(function(){
            transactionNames.push($(this).html().toLowerCase());
        });
        $("body").data("transaction-names", transactionNames);
    }
    
    $('.add-transaction').each(function(){
        var $el = $(this);
        $(this).qtip({
            overwrite: true,
            content: {
                text: function(api) {
                    $('#create-mini-transaction-form').show();
                    return $('#create-mini-transaction-form')
                }
            },
            events: {
                visible: function(event, api) {
                    onVisibleMiniTransaction($el);
                }, 
                hidden: function(event, api) {
                    onHiddenMiniTransaction();
                }
            },
            show: {
                event: 'click'
            },
            hide: {
                event: 'unfocus'
            }
        });
    });
    
    
    $("body").click(function(event){
        if($(event.target).hasClass("delete-transaction")){
            var $parent = $(event.target).parents(".transaction-container");
            var name = $parent.hasClass("js-transaction-income") ? Contabilidad.tr("ingreso") : Contabilidad.tr("egreso");
            var output = Mustache.render($("#delete-popup-tpl").html(), 
                        {message: Contabilidad.tr("¿Realmente quieres eliminar este %s?", name)});
            $.fancybox({
                'content' : output,
                'onComplete' : function(){
                    $("#delete-popup input[type='button']").click(function(){
                        if($(this).attr("id") == "yes"){
                            var id = $parent.attr("data-id");
                            deleteTransaction(id);
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                                $(".account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency));
                            }}).deleteTransaction(id, Contabilidad.account.id);
                        }
                        $.fancybox.close();
                    });
                }
            });
            return false;
        } else if($(event.target).hasClass("edit-transaction")){//edit transaction
            var nAddFrag = document.createDocumentFragment();
            if(!$(event.target).data("el")){
                var el = document.getElementById("create-transaction-form");
                $(event.target).data("el", el);
            }
            nAddFrag.appendChild($(event.target).data("el"));
            var $div = $("<div>").append(nAddFrag);
            var id = $(event.target).parents(".transaction-container").attr("data-id");
            var curTran = Contabilidad.transactions[id];
            $div.find("input[name='id']").val(id);
            $div.find("input[name='name']").val(curTran.name);
            $div.find("input[name='id_category_type']").val(curTran.id_category_type);
            $div.find("input[name='id_category_type']").val(curTran.id_category_type);
            $div.find("input[name='value']").val(curTran.value);
            $div.find("input[name='date']").val(curTran.date);
            if(curTran.is_frequent){
                $div.find("input[name='is_frequent']").attr('checked', true).trigger("change");
                if(!(curTran.frequency_days == 1 || curTran.frequency_days == 7 
                     || curTran.frequency_days == 15 || curTran.frequency_days == -1)){
                    $div.find("input[name='precise_frequency_days']").val(curTran.frequency_days).attr("disabled", false);
                    $div.find("select[name='frequency_days']").val(0).attr("disabled", false);
                } else {
                    $div.find("select[name='frequency_days']").val(curTran.frequency_days).attr("disabled", false);
                }
                $div.find("select[name='frequency_time']").val(curTran.frequency_time).attr("disabled", false);
            }
            showTransactionPopup($div, $(event.target));
            return false;
        } else if($(event.target).hasClass("edit-account")){
            QHelpers.account.showBalancePopup($(event.target) , Contabilidad.account);
        } else if($(event.target).hasClass("delete-account")){
            var output = Mustache.render($("#delete-popup-tpl").html(), 
                        {message: Contabilidad.tr("¿Realmente quieres eliminar este Balance?")});
            var $parent = $(event.target).parents(".account-container");
            $.fancybox({
                'content' : output,
                'onComplete' : function(){
                    $("#delete-popup input[type='button']").click(function(){
                        if($(this).attr("id") == "yes"){
                            var id = $parent.attr("data-id");
                            $parent.remove();
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                                document.location.href = Contabilidad.private_home;
                            }}).deleteAccount(id);
                        }
                        $.fancybox.close();
                    });
                }
            });
            return false;
        } else if($(event.target).attr("id") == "delete-outside-transactions"){
            var output = Mustache.render($("#delete-popup-tpl").html(), 
                        {message: Contabilidad.tr("¿Realmente quieres eliminar las transacciones?")});
            var $parent = $(event.target).parents(".account-container");
            $.fancybox({
                'content' : output,
                'onComplete' : function(){
                    $("#delete-popup input[type='button']").click(function(){
                        if($(this).attr("id") == "yes"){
                            var ids = [];
                            $("#outside-transactions-container .transaction-container").each(function(){
                                var id = $(this).attr("data-id");
                                ids.push(id);
                                deleteTransaction(id);
                            });
//                            var id = $parent.attr("data-id");
//                            $parent.remove();
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                                
                            }}).deleteTransactions(ids);
                        }
                        $.fancybox.close();
                    });
                }
            });
            return false;
        }
    });
});

/*************************************
 **********TRANSACTION METHODS***********
 *************************************/
function onVisibleMiniTransaction($el){
    
    var transactionName = getNewTransactionName($el);
    
    $("#create-mini-transaction-form input[name='name']").val(transactionName).select();
    $("#create-mini-transaction-form input[name='value']").focus();
    
    //rules
    $("#create-mini-transaction-form input[type='text']").each(function(){
        setInputRule($(this));
    });
    
    $('#create-mini-transaction-form form').submit(function(){
        Contabilidad.Validate.clean($('#create-mini-transaction-form'));
        $('#create-mini-transaction-form .response')
        .html("").removeClass("*").addClass("response");
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            data.name = $("#create-mini-transaction-form input[name='name']").val();
            data.value = $("#create-mini-transaction-form input[name='value']")
            .val().replace(/\./g, '').replace(/,/g, '.');
            data.id_account = parseInt(Contabilidad.getURLParameter("id"));
            data.id_transaction_type = $el.attr("id") == "add-income" ? 1 : 2;
            Contabilidad.getEndPoint({async : true, success: function(resp){
                displaySavedTransactions(resp);
                $el.qtip('hide');
            }}).saveTransaction(data);
        } else {
            findAndDisplayErrors($("#create-mini-transaction-form").parent());
        }
        return false;
    });

    addMoneyBehavior($("#create-mini-transaction-form input[name='value']"));
    
    //more options->open big transaction form
    $(".more-options").click(function(){
        $el.qtip('hide');
        var nAddFrag = document.createDocumentFragment();
        if(!$(this).data("el")){
            var el = document.getElementById("create-transaction-form");
            $(this).data("el", el);
        }
        nAddFrag.appendChild($(this).data("el"));
        var $div = $("<div>").append(nAddFrag);
        showTransactionPopup($div, $el);
        return false;
    });
}

/****************************************************
 ************ SHOW TRANSACTION POPUP ****************
 ****************************************************/

function showTransactionPopup($div, $el){
    $.fancybox({
        content : $div,
        'onStart' : onCreateTransactionStart($div),
        'onCleanup' : function(){
            this.form = document.getElementById("create-transaction-form");
            if($div.find("input[name='is_frequent']").is(":checked")){
                $div.find("input[name='is_frequent']").attr('checked', false).trigger("change");
            }
            $(this.form).find("#remove-frequency-warning").hide().css({opacity: 1}).stop().fadeOut();
            $div.find("select[name='frequency_days']").val("1");
            $div.find("input[name='precise_frequency_days']").val("");
            $div.find("select[name='frequency_time']").val("0");
            $div.find("input[name='id']").val("0");
            $div.find(".less-options").show();
        },
        'onClosed' : function(){
            onClose(this.form);
            $(this.form).find(".hasDatepicker").removeClass("hasDatepicker");
        },
        'onComplete' : function(){
            $div = this.form ? this.form : $div;
            if(parseInt($div.find("input[name='id']").val())){
                onEditTransactionComplete($div, $el);
            } else {
                onCreateTransactionComplete($div, $el);
            }
        }
    });
};

/****************************************************
 ************ SHOW TRANSACTION POPUP ****************
 ****************************************************/

function onEditTransactionComplete($div, $el){
     //rules
    $div.find("#create-transaction-form input[type='text']").each(function(){
        setInputRule($(this));
    });
    
    addMoneyBehavior($div.find("#create-transaction-form input[name='value']"));
    
    var id = $el.parents(".transaction-container").attr("data-id");
    var curTran = Contabilidad.transactions[id];
    $div.find("input[name='id_transaction_type']").val(curTran.id_transaction_type);
    
    transactionPoppupCommonEvents($div, curTran.timestampDate);
};

function onHiddenMiniTransaction(){
    $("#create-mini-transaction-form input[name='value']")
    .unbind("keypress").unbind("keyup");
    $(".more-options").unbind("click");
    $('#create-mini-transaction-form form').unbind("submit");
    $('#create-mini-transaction-form .response')
    .html("").removeClass("*").addClass("response");
    $("#create-mini-transaction-form input[type='text']").each(function(){
        $(this).val("");
    });
}

function getNewTransactionName($el){
    //transaction name
    var defaultNumber = 1;
    var defaultName = $el.attr("id") == "add-income" ? Contabilidad.tr("Ingreso ") :  Contabilidad.tr("Egreso ");
    var transactionName = defaultName + defaultNumber;
    
    while($.inArray(transactionName.toLowerCase(), $("body").data("transaction-names")) >= 0){
        defaultNumber++;
        transactionName = defaultName + defaultNumber;
    }
    return transactionName;
}

/*************************************
 ****CREATE BIG TRANSACTION METHODS***
 *************************************/

function onCreateTransactionStart($div){
    $div.find("#create-transaction-form").show();
    if(parseInt($div.find("input[name='id']").val())){
        $div.find("input[type='submit']").val(Contabilidad.tr("Editar"));
        $div.find(".less-options").hide();
    } else {
        $div.find("input[type='submit']").val(Contabilidad.tr("Crear"));
    }
}

function onCreateTransactionComplete($div, $el){
    var transactionName = getNewTransactionName($el);
    
    $div.find("#create-transaction-form input[name='name']").val(transactionName).select();
    $div.find("#create-transaction-form input[name='value']").focus();
    $div.find("input[name='id_transaction_type']").val($el.attr("id") == "add-income" ? 1 : 2);
    
    //less options link
    $div.find(".less-options").click(function(){
        $.fancybox.close();
        $el.qtip('show');
        return false;
    });
    
    var date = $div.find(".js-time").html();
    
    //if currentdate is not inside account period
    if(date < Contabilidad.account.date_ini){
        date = Contabilidad.account.date_ini;
    } else if(date > Contabilidad.account.date_end){
        date = Contabilidad.account.date_end;
    }
    
    transactionPoppupCommonEvents($div, date);
}

/*************************************
 **********ALL FORMS METHODS***********
 *************************************/

function onClose(form){
    Contabilidad.Validate.clean($(form));
    $(form).hide();
    $(form).find("input[type='text'], input[type='password']").val('');
    $(form).find(".response").html("")
    .removeClass("*")
    .addClass("response")
    .html("");
    var nAddFrag = document.createDocumentFragment();
    nAddFrag.appendChild(form);
    $("body").append(nAddFrag);
}

function addMoneyBehavior($el){
    $el.keypress(function(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if ((charCode > 31 && (charCode < 48 || charCode > 57)) &&
        (charCode != 44 && charCode != 46)){
            return false;
        }
        return true;
    }).keyup(function(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if ((charCode > 36 && charCode < 40) || charCode==16){
            return true;
        } else {
            var value = this.value.replace(/\./g, '');
            value = value.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
            $(this).val(value);
        }
    });
}

/*************************************
 **********VALIDATE METHODS***********
 *************************************/

function findAndDisplayErrors($form)
{
    var errors;
    $form.find(".input-error").each(function(){
        errors =  $(this).data("errors");
        if(errors.length){
            return;
        }
        return;
    });
    $form.find(".response").html(errors[0].message).show();
}

//set rules to an input
function setInputRule($input){
    if($input.attr("type") == "submit") return;
    //validation rulesc
    var rules = {required : $input.hasClass("required")};
    if($input.hasClass("money")){rules.money = true;}
    if($input.hasClass("is_email")){rules.email = true;}
    if($input.attr("type") == "password"){rules.password = true;}
    if($input.attr("is_equal_to")){rules.equalsTo = $input.parent().find("input[name='" + $input.attr("is_equal_to") + "']");}
    Contabilidad.Validate.setRules($input, rules);
}

/*************************************
 ********** COMMON EVENTS ************
 *************************************/

function transactionPoppupCommonEvents($div, date){
    var currentDate = new Date(parseInt(date)*1000);
     //rules
    $div.find("#create-transaction-form input[type='text']").each(function(){
        setInputRule($(this));
    });
    
    addMoneyBehavior($div.find("#create-transaction-form input[name='value']"));
    $div.find("input[name='date']")
    .val(Contabilidad.toDate(date))
    .datepicker({defaultDate: currentDate,
                 minDate: new Date(parseInt(Contabilidad.account.date_ini)*1000),
                 maxDate: new Date(parseInt(Contabilidad.account.date_end)*1000),
                 dateFormat: "dd/mm/yy"
     });
    
    //frecuency days select
    $div.find("select[name='frequency_days']").change(function(){
        if(this.value == 0){
            $div.find("input[name='precise_frequency_days']").removeAttr("disabled");
        } else {
            $div.find("input[name='precise_frequency_days']").attr("disabled", true);
        }
    });
    
    //frequent checkbox
    $div.find("input[name='is_frequent']").change(function(){
        if($(this).is(":checked")){
            $div.find("select[name='frequency_days']").removeAttr("disabled");
            $div.find("select[name='frequency_time']").removeAttr("disabled");
            if($div.find("select[name='frequency_days']").val() == 0){
                $div.find("input[name='precise_frequency_days']").removeAttr("disabled");
            }
            $div.find("#remove-frequency-warning").css({opacity: 1}).hide().stop().fadeOut();
        } else {
            var id = parseInt($div.find("input[name='id']").val());
            if(id){
                var curTran = Contabilidad.transactions[id];
                if(curTran.is_frequent){
                    $div.find("#remove-frequency-warning").show().fadeOut(5000, function(){
                        $(this).css({opacity: 1});
                    });
                }
            }
            $div.find("select[name='frequency_days']").attr("disabled", true);
            $div.find("select[name='frequency_time']").attr("disabled", true);
            $div.find("input[name='precise_frequency_days']").attr("disabled", true);
        }
    });
    
    $div.find("#create-transaction-form form").submit(function(){
        Contabilidad.Validate.clean($('#create-mini-transaction-form'));
        $('#create-mini-transaction-form .response')
        .html("").removeClass("*").addClass("response");
        var data = {};
        data["date"] = $(this).find("input[name='date']").datepicker("getDate").getTime()/1000;
        if(Contabilidad.Validate.isValid($(this))
           && data["date"] >= Contabilidad.account.date_ini
           && data["date"] <= Contabilidad.account.date_end){
           
            $(this).find("input[type='text'],input[type='hidden'],select").each(function(){
                if($(this).attr("name") == "date") return;
                data[$(this).attr("name")] = this.value;
            });
            data["is_frequent"] = $(this).find("input[name='is_frequent']").is(":checked");
            if(!data["is_frequent"]){
                data = {"date" : data["date"], "name" : data["name"],
                         "value" : data["value"], "id_category_type" : data["id_category_type"],
                         "id_transaction_type" : data["id_transaction_type"], "id": data["id"]};
            } else {
                if(data.frequency_days == 0) data.frequency_days = data.precise_frequency_days;
            }
            data.id_account = Contabilidad.account.id;
            data.value = data.value.replace(/\./g, '').replace(/,/g, '.');
            Contabilidad.getEndPoint({async : true, success: function(resp){
                displaySavedTransactions(resp);
            }}).saveTransaction(data);
            $.fancybox.close();
        } else {
            if(data["date"] < Contabilidad.account.date_ini
               || data["date"] > Contabilidad.account.date_end){
               $(this).find("input[name='date']").addClass("input-error")
                .data("errors",[{message : Contabilidad.tr("La fecha de la transaccion debe estar dentro del periodo del balance.")}]);
            }
            findAndDisplayErrors($div);
        }
        return false;
    });
};

function displaySavedTransactions(resp)
{
    $(resp.transactions).each(function(i){
        var tran = resp.transactions[i];
        if(Contabilidad.transactions[tran.id]){
            $("#transaction-" + tran.id + " .js-transaction-name").html(tran.name);
            $("#transaction-" + tran.id + " .js-transaction-date").html(tran.date);
            $("#transaction-" + tran.id + " .js-transaction-value").html(tran.value);
        } else {
            if(!$("#transactions-title").length){
                var h3 = document.createElement("h3");
                h3.id = "transactions-title";
                $(h3).html(Contabilidad.tr("Transacciones"));
                $("#transactions-container").before($(h3));
            }
            var output = Mustache.render($("#transaction-row-tpl").html(), tran);
            $("#transactions-container").prepend(output);
        }
        $("body").data("transaction-names").push(tran.name.toLowerCase());
        $(".account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency));

        //update temporal variable
        Contabilidad.transactions[tran.id] = tran;
    });
    
    //remove transactions
    if(resp.deleted_transactions){
        $(resp.deleted_transactions).each(function(i){
            var id = resp.deleted_transactions[i];
            deleteTransaction(id);
        });
    }
}

function deleteTransaction(id){
    $("#transaction-" + id).remove();
    delete Contabilidad.transactions[id];
    
    if(!$("#transactions-container .transaction-container").length){
        $("#transactions-title").remove();
    }
    if(!$("#outside-transactions-container .transaction-container").length){
        $("#outside-transactions-container").remove();
        $("#outside-transactions-title").remove();
    }
}
