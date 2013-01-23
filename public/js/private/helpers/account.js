window.QHelpers = window.QHelpers || {};

window.QHelpers.account = {};

QHelpers.account.showBalancePopup = function ($el, account){
   var nAddFrag = document.createDocumentFragment();
        if(!$el.data("el")){
            var el = document.getElementById("create-account-form");
            $el.data("el", el);
        }
        nAddFrag.appendChild($el.data("el"));
        var $div = $("<div>").append(nAddFrag);
        if (account){
            $div.find("#create-account-form .title").html(Contabilidad.tr("Editar balance"));
            $div.find("#create-account-form input[type='submit']").val(Contabilidad.tr("Guardar"));
            $div.find("#create-account-form input[name='name']").val(account.name);
            var dateIni = new Date(parseInt(account.date_ini)*1000);
            $div.find("#create-account-form input[name='date_ini']")
            .val(dateIni.getDate() + "/" + (dateIni.getMonth() + 1) + "/" + dateIni.getFullYear())
            .datepicker({defaultDate: dateIni , dateFormat: "dd/mm/yy"});
            var dateEnd = new Date(parseInt(account.date_end)*1000);
            $div.find("#create-account-form input[name='date_end']")
            .val(dateEnd.getDate() + "/" + (dateEnd.getMonth() + 1) + "/" + dateEnd.getFullYear())
            .datepicker({defaultDate: dateEnd , dateFormat: "dd/mm/yy"});
            $div.find("#create-account-form").show();
            $div.find("#create-account-form input").each(function(){
                setInputRule($(this));
            });
            $.fancybox({
                'content' : $div,
                'onStart' : QHelpers.account.onAccountPopupStart($div, account),
                'onCleanup' : function(){
                    this.form = document.getElementById("create-account-form");
                },
                'onClosed' : function(){
                    onClose(this.form);
                    $(this.form).find(".hasDatepicker").removeClass("hasDatepicker");
                }
            });
            $div.find("#create-account-form").show();
            $div.find("#create-account-form input").each(function(){
                setInputRule($(this));
            });
        } else {
            var dateIni = new Date(parseInt($div.find(".js-time").html()));
            var dateEnd = new Date(parseInt($div.find(".js-time").html()) + (60*60*24*30));
            $div.find("#create-account-form .title").html(Contabilidad.tr("Crear balance"));
            $div.find("#create-account-form input[type='submit']").val(Contabilidad.tr("Crear"));
            $div.find("#create-account-form input[name='date_ini']")
            .val(Contabilidad.toDate(dateIni))
            .datepicker({defaultDate: dateIni , dateFormat: "dd/mm/yy"});
            $div.find("#create-account-form input[name='date_end']")
            .val(Contabilidad.toDate(dateEnd))
            .datepicker({defaultDate: dateEnd , dateFormat: "dd/mm/yy"});
            $div.find("#create-account-form").show();
            $div.find("#create-account-form input").each(function(){
                setInputRule($(this));
            });
            $.fancybox({
                'content' : $div,
                'onStart' : QHelpers.account.onAccountPopupStart($div, account),
                'onCleanup' : function(){
                    this.form = document.getElementById("create-account-form");
                },
                'onClosed' : function(){
                    onClose(this.form);
                    $(this.form).find(".hasDatepicker").removeClass("hasDatepicker");
                },
                'onComplete' : function(){
                    $div = this.form ? this.form : $div;
                    QHelpers.account.onCreateAccountComplete($div);
                }
            });
        }
            $div.find("#create-account-form").show();
            $div.find("#create-account-form input").each(function(){
            setInputRule($(this));
        });
    return false;
}


QHelpers.account.onAccountPopupStart = function ($div, account){
    if (account){
        $div.find("form").submit(function(){
        var date_ini = $div.find("input[name='date_ini']").datepicker("getDate").getTime()/1000;
        var date_end = $div.find("input[name='date_end']").datepicker("getDate").getTime()/1000;
        if(Contabilidad.Validate.isValid($(this)) && date_end >= date_ini){
            var data = {};
            $(this).find("input,select").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            data.date_ini = date_ini;
            data.date_end = date_end;
            data.id = account.id;
            Contabilidad.getEndPoint({async : true, success: function(resp){
                $("#account-"+resp.account.id).find(".js-account-name").html(resp.account.name);
                $("#account-"+resp.account.id).find(".js-account-date_ini").html(Contabilidad.toDate(data.date_ini));
                $("#account-"+resp.account.id).find(".js-account-date_end").html(Contabilidad.toDate(data.date_end));
                if ((resp.account.date_ini != account.date_ini || resp.account.date_end != account.date_end)
                    && (Contabilidad.controller == "account" && Contabilidad.action == "index")){
                    location.reload(true);
                }
                Contabilidad.account = resp.account;
                $("#account-" + resp.account.id + " .account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency));
                $("#account-"+resp.account.id).find(".js-account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency))
                $.fancybox.close();
            }}).editAccount(data);
        } else {
            if(date_end < date_ini) {
                $div.find("input[name='date_end']").addClass("input-error")
                .data("errors",[{message : Contabilidad.tr("La fecha final debe ser posterior a la fecha inicial.")}]);
            }
            findAndDisplayErrors($div.find("#create-account-form"));
        }
        return false;
    });

    } else {
        
        var currentDate = new Date(parseInt($div.find(".js-time").html())*1000);
        var thisMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        var monthLater = new Date((thisMonth.getTime()/1000 + 60*60*24*30 )*1000);
        //date ini
        $div.find("input[name='date_ini']")
        .val(Contabilidad.toDate(thisMonth.getTime()/1000))
        .datepicker({defaultDate: thisMonth , dateFormat :"dd/mm/yy"});
        //end date
        $div.find("input[name='date_end']")
        .val(Contabilidad.toDate(monthLater.getTime()/1000))
        .datepicker({defaultDate: monthLater  , dateFormat :"dd/mm/yy"});
        
        var defaultName = Contabilidad.tr("Balance ");
        var defaultNumber = 1;
        var accountName = defaultName + defaultNumber;
        if(!$("body").data("account-names")){
            var accountNames = [];
            $(".js-account-name").each(function(){
                accountNames.push($(this).html().toLowerCase());
            });
            $("body").data("account-names", accountNames);
        }
        while($.inArray(accountName.toLowerCase(), $("body").data("account-names")) >= 0){
            defaultNumber++;
            accountName = defaultName + defaultNumber;
        }
        $div.find("input[name='name']").val(accountName);

        $div.find("form").submit(function(){
            var date_ini = $div.find("input[name='date_ini']").datepicker("getDate").getTime()/1000;
            var date_end = $div.find("input[name='date_end']").datepicker("getDate").getTime()/1000;
            if(Contabilidad.Validate.isValid($(this)) && date_end >= date_ini){
                var data = {};
                $(this).find("input,select").each(function(){
                    data[$(this).attr("name")] = $(this).val();
                });
                data.date_ini = date_ini;
                data.date_end = date_end;
                Contabilidad.getEndPoint({async : true, success: function(resp){
                    resp.account.date_ini = Contabilidad.toDate(resp.account.date_ini);
                    resp.account.date_end = Contabilidad.toDate(resp.account.date_end);
                    $("body").data("account-names").push(resp.account.name.toLowerCase());
                    var output = Mustache.render($("#account-row-tpl").html(), resp.account);
                    $("#accounts-container").prepend(output);
                    $.fancybox.close();
                }}).createAccount(data);
            } else {
                if(date_end < date_ini) {
                    $div.find("input[name='date_end']").addClass("input-error")
                    .data("errors",[{message : Contabilidad.tr("La fecha final debe ser posterior a la fecha inicial.")}]);
                }
                findAndDisplayErrors($div.find("#create-account-form"));
            }
            return false;
        });
    }

    QHelpers.account.onCreateAccountComplete = function ($div){
        //select name
        $div.find("input[name='name']").select();
    }
}