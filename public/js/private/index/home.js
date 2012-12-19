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
});

/*************************************
 *******CREATE ACCOUNT METHODS********
 *************************************/

function onCreateAccountStart($div){
    $div.find("#create-account-form").show();
    
//    $div.find("#create-account-form input").each(function(){
//        setInputRule($(this));
//    });
    
    //CREATE ACCOUNT SUBMIT
    $div.find("form").submit(function(){
//        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            console.info(data);
            $.fancybox.close();
//            var resp = Contabilidad.getEndPoint({async : true, success: function(resp){
//                if(resp.result == "success"){
//                    document.location.href = Contabilidad.private_home;
//                } else if(resp.result == "failure") {
//                    if(resp.reason == USER_NOT_FOUND){
//                        $div.find(".response")
//                        .addClass("error")
//                        .html(Contabilidad.tr("correo o contraseña incorrectos"));
//                    }
//                }
//            }}).login(data);
//        } else {
//            findAndDisplayErrors($div.find("#create-account-form"));
//        }
        return false;
    });
}

function onCreateAccountComplete ($div){
    console.info($div.find(".date"));
    $div.find(".date").datepicker();
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