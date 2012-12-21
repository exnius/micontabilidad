$(document).ready(function(){
    
    //CREATE ACCOUNT FANCYBOX
    $("#js-fancy-crear-contabilidad").click(function(){
        var nAddFrag = document.createDocumentFragment();
        if(!$(this).data("el")){
            var el = document.getElementById("create-account-form");
            $(this).data("el", el);
        }
        nAddFrag.appendChild($(this).data("el"));
        var $div = $("<div>").append(nAddFrag);
        
        $.fancybox({
            'content' : $div,
            'onStart' : onCreateAccountStart($div),
            'onCleanup' : function(){
                this.form = document.getElementById("create-account-form");
            },
            'onClosed' : function(){
                onClose(this.form);
                $(this.form).find(".hasDatepicker").removeClass("hasDatepicker");
            },
            'onComplete' : function(){
                $div = this.form ? this.form : $div;
                onCreateAccountComplete($div);
            }
        });
        return false;
    });
    
    $("body").click(function(event){
        if($(event.target).hasClass("delete-account")){
            var output = Mustache.render($("#delete-popup-tpl").html(), 
                        {message: Contabilidad.tr("¿Realmente quieres eliminar este balance?")});
            $.fancybox({
                'content' : output,
                'onComplete' : function(){
                    $("#delete-popup input[type='button']").click(function(){
                        if($(this).attr("id") == "yes"){
                            var $el = $(event.target).parent(".account-container");
                            $el.remove();
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                            }}).deleteAccount($el.attr("data-id"));
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
 *******CREATE ACCOUNT METHODS********
 *************************************/

function onCreateAccountStart($div){
    $div.find("#create-account-form").show();
    $div.find("#create-account-form input").each(function(){
        setInputRule($(this));
    });
    
    //CREATE ACCOUNT SUBMIT
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

function onCreateAccountComplete ($div){
    
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
    
    var currentDate = new Date(parseInt($div.find(".js-time").html())*1000);
    var monthLater = new Date((parseInt($div.find(".js-time").html())  + 60*60*24*30 )*1000);
    //date ini
    $div.find("input[name='date_ini']")
    .val(currentDate.getDate() + "/" + (currentDate.getMonth() + 1) + "/" + currentDate.getFullYear())
    .datepicker({defaultDate: currentDate});
    //end date
    $div.find("input[name='date_end']")
    .val(monthLater.getDate() + "/" + (monthLater.getMonth() + 1) + "/" + monthLater.getFullYear())
    .datepicker({defaultDate: monthLater});
    
    //select name
    $div.find("input[name='name']").select();
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
    .html("");;
    var nAddFrag = document.createDocumentFragment();
    nAddFrag.appendChild(form);
    $("body").append(nAddFrag);
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
    if($input.hasClass("is_email")){rules.email = true;}
    if($input.attr("type") == "password"){rules.password = true;}
    if($input.attr("is_equal_to")){rules.equalsTo = $input.parent().find("input[name='" + $input.attr("is_equal_to") + "']");}
    Contabilidad.Validate.setRules($input, rules);
}