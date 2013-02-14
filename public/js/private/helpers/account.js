window.QHelpers = window.QHelpers || {};

window.QHelpers.account = {};

QHelpers.account.showBalancePopup = function ($el, account){
    var el = document.getElementById("create-account-form").innerHTML;
    var $div = $("<div>").append(el);
    $div.find("textarea#account-details").val("");
    $div.find('#account-chars-label label.charsLeft').html(140);
    $div.find("#account-iframe-container iframe").attr("src", BASE_URL + "/private/account/iframe");
    if (account){//edit
        //title
        $div.find(".title").html(Contabilidad.tr("Editar presupuesto"));
        //boton
        $div.find("input[type='submit']").val(Contabilidad.tr("Guardar"));
        //name
        $div.find("input[name='name']").val(account.name);
        //image
        $div.find("#account-picture").attr("src", account.picture_url);
        //details
        $div.find("textarea#account-details").val(account.details);
        $div.find('#account-chars-label label.charsLeft').html(140 - $div.find("textarea#account-details").val().length);
        if(parseInt(account.is_independent)) $div.find("input[name='is_independent']").attr('checked', true);
        
        //date ini
        var dateIni = new Date(parseInt(account.date_ini)*1000);
        $div.find("input[name='date_ini']")
        .val(Contabilidad.toDate(dateIni.getTime()/1000))
        .datepicker({defaultDate: dateIni , dateFormat: "dd/mm/yy"});
        //date end
        var dateEnd = new Date(parseInt(account.date_end)*1000);
        $div.find("input[name='date_end']")
        .val(Contabilidad.toDate(dateEnd.getTime()/1000))
        .datepicker({defaultDate: dateEnd , dateFormat: "dd/mm/yy"});
        $div.find("input").each(function(){
            setInputRule($(this));
        });
        $.fancybox({
            'content' : $div,
            'onStart' : QHelpers.account.onAccountPopupStart($div, account)
        });
        $div.find("input").each(function(){
            setInputRule($(this));
        });
    } else {//create
        var dateIni = new Date(parseInt($("#js-time").html()));
        var dateEnd = new Date(parseInt($("#js-time").html()) + (60*60*24*30));
        $div.find(".title").html(Contabilidad.tr("Crear presupuesto"));
        $div.find("input[type='submit']").val(Contabilidad.tr("Crear"));
        //image
        $div.find("#account-picture").attr("src", LINKS_URL + "/quantups_pictures/budget.png");
        //date ini
        $div.find("input[name='date_ini']")
        .val(Contabilidad.toDate(dateIni))
        .datepicker({defaultDate: dateIni , dateFormat: "dd/mm/yy"});
        //date end
        $div.find("input[name='date_end']")
        .val(Contabilidad.toDate(dateEnd))
        .datepicker({defaultDate: dateEnd , dateFormat: "dd/mm/yy"});
        //details
        $div.find('#account-chars-label label.charsLeft').val(140);
        
        $div.find("input").each(function(){
            setInputRule($(this));
        });
        
        $.fancybox({
            'content' : $div,
            'onStart' : QHelpers.account.onAccountPopupStart($div, account),
            'onComplete' : function(){
                QHelpers.account.onCreateAccountComplete($div);
            }
        });
    }
    $div.find("input").each(function(){
        setInputRule($(this));
    });
    return false;
}


QHelpers.account.onAccountPopupStart = function ($div, account){
    if (account){//edit
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
                data.is_independent = $div.find("input[name='is_independent']").is(":checked");
                data.picture_url = $div.find("#account-picture").attr("src");
                data.details = $div.find("textarea#account-details").val();
                Contabilidad.getEndPoint({async : true, success: function(resp){
                    $("#account-"+resp.account.id).find(".js-account-name").html(resp.account.name);
                    $("#account-"+resp.account.id).find(".js-account-date_ini").html(Contabilidad.toDate(data.date_ini));
                    $("#account-"+resp.account.id).find(".js-account-date_end").html(Contabilidad.toDate(data.date_end));
                    if ((resp.account.date_ini != account.date_ini || resp.account.date_end != account.date_end
                        || resp.account.is_independent != account.is_independent)
                        && (Contabilidad.controller == "account" && Contabilidad.action == "index")){
                        location.reload(true);
                    } else if(Contabilidad.controller == "account" && Contabilidad.action == "index"){
                        Contabilidad.account = resp.account;
                    } else {
                        Contabilidad.accounts[resp.account.id] = resp.account;
                    }
                    $("#account-" + resp.account.id + " .account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency));
                    $("#account-"+resp.account.id).find(".js-account-benefit").html(Contabilidad.currencyValue(resp.account.benefit, resp.account.id_currency))
                    $("#account-"+resp.account.id).find(".js-account-picture img").attr("src", resp.account.picture_url);
                    $.fancybox.close();
                }}).editAccount(data);
            } else {
                if(date_end < date_ini) {
                    $div.find("input[name='date_end']").addClass("input-error")
                    .data("errors",[{message : Contabilidad.tr("La fecha final debe ser posterior a la fecha inicial.")}]);
                }
                findAndDisplayErrors($div);
            }
            return false;
        });

    } else {
        
        var currentDate = new Date(parseInt($("#js-time").html())*1000);
        var thisMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        var monthLater = new Date((thisMonth.getTime()/1000 + 60*60*24*30 )*1000);
        
        var monthInfo = Contabilidad.getMonthInfo(thisMonth);
        //date ini
        $div.find("input[name='date_ini']")
        .val(Contabilidad.toDate(thisMonth.getTime()/1000))
        .datepicker({defaultDate: thisMonth , dateFormat :"dd/mm/yy"});
        //end date
        $div.find("input[name='date_end']")
        .val(Contabilidad.toDate(monthInfo.date.getTime()/1000))
        .datepicker({defaultDate: monthInfo.date  , dateFormat :"dd/mm/yy"});
        
        
        var defaultName = monthInfo.name.capitalize();
        var defaultNumber = 1;
        var accountName = defaultName;
        if(!$("body").data("account-names")){
            var accountNames = [];
            $(".js-account-name").each(function(){
                accountNames.push($(this).html().toLowerCase());
            });
            $("body").data("account-names", accountNames);
        }
        while($.inArray(accountName.toLowerCase(), $("body").data("account-names")) >= 0){
            defaultNumber++;
            accountName = defaultName + " " + defaultNumber;
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
                data.is_independent = $div.find("input[name='is_independent']").is(":checked");
                data.date_ini = date_ini;
                data.date_end = date_end;
                data.picture_url = $div.find("#account-picture").attr("src");
                data.details = $div.find("textarea#account-details").val();
                Contabilidad.getEndPoint({async : true, success: function(resp){
                    resp.account.date_ini = Contabilidad.toDate(resp.account.date_ini);
                    resp.account.date_end = Contabilidad.toDate(resp.account.date_end);
                    $("body").data("account-names").push(resp.account.name.toLowerCase());
                    var output = Mustache.render($("#account-row-tpl").html(), resp.account);
                    $("#accounts-container").prepend(output);
                    Contabilidad.accounts[resp.account.id] = resp.account;
                    $.fancybox.close();
                }}).createAccount(data);
            } else {
                if(date_end < date_ini) {
                    $div.find("input[name='date_end']").addClass("input-error")
                    .data("errors",[{message : Contabilidad.tr("La fecha final debe ser posterior a la fecha inicial.")}]);
                }
                findAndDisplayErrors($div);
            }
            return false;
        });
    }
    
    $div.find("textarea#account-details").keyup(function(){
        var len = $(this).val().length;
        if (len > 140) {
            this.value = this.value.substring(0, 140);
            len = this.value.length;
        }
        $div.find('#account-chars-label label.charsLeft').text(140 - len);
    }).keydown(function(){
        var len = $(this).val().length;
        $div.find('#account-chars-label label.charsLeft').text(140 - len);
    });
}

QHelpers.account.onCreateAccountComplete = function ($div){
    //select name
    $div.find("input[name='name']").select();
}

QHelpers.account.uploadPicture = function(fileObj){
    var par = window.document;
    var frm = fileObj.form;

    $("#picture-response").hide();

    // create new iframe
    var new_iframe = par.createElement('iframe');
    new_iframe.src = BASE_URL + "/private/account/iframe";
    new_iframe.frameBorder = '0';
    new_iframe.scrolling = 'no';
    new_iframe.marginHeight = '0';
    new_iframe.marginWidth = '0';
    new_iframe.style.height = '75px';
    new_iframe.style.width = '500px';

    //hide old iframe
    var iframes = $("#account-iframe-container iframe");
    var iframe = iframes[iframes.length - 1];
    iframe.style.display = 'none';
    //append the new iframe
    $("#account-iframe-container").append(new_iframe);

    iframe.id = 'old-iframe';

    // send
    frm.submit();
}

QHelpers.account.setUploadedImage = function(resp){
    if(resp.response == "success"){
        $("#account-picture").attr("src", resp.url);
    } else {
        $("#picture-response").removeClass("*")
        .addClass("error")
        .html(Contabilidad.tr("Lo sentimos, ocurrio un problema al subir tu imagen. Intenta con otra!"))
        .show();
    }
    var iframe = $("#account-iframe-container #old-iframe");
    $(iframe).remove();
}