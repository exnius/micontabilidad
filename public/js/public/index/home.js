const USER_NOT_FOUND = "wrong authentication";
const EMAIL_ALREADY_REGISTERED = "email already registered";

$(document).ready(function(){
    
    //default input values and setting validate rules
    $(".js-fancy-login").fancybox({
        'content' : $("#login-form"),
        'onStart' : onLoginStart(),
        'onCleanup' : function(){
            $("#login-form input[type='text'], #login-form input[type='password']").val('');
            Contabilidad.Validate.clean($("#login-form"));
            $("#login-form .error").html("");
        },
        'onComplete' : function(){
            onLoginComplete()
        }
    });
    
    $(".js-fancy-register").fancybox({
        'content' : $("#register-form"),
        'onStart' : function(){
            onRegisterStart()
        },
        'onComplete' : function(){
            onRegisterComplete()
        },
        'onCleanup' : function(){
            Contabilidad.Validate.clean($("#register-form"));
            $("#register-form input[type='text'], #register-form input[type='password']").val('');
            $("#register-form .error").html("");
        }
    });
});

function onRegisterStart(){
    $("#register-form").show();
}

function onRegisterComplete (){
    $("#register-form input").each(function(){
        setInputRule($(this));
    });
    
    //REGISTER SUBMIT
    $("#register-form form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            var resp = Contabilidad.endPoint({async: true, success: function(resp){
                if(resp.result == "success"){
                    document.location.href = Contabilidad.private_home;
                } else if(resp.result == "failure") {
                    if(resp.reason == EMAIL_ALREADY_REGISTERED){
                        $("#register-form .error").html(Contabilidad.tr("Ya existe usuario con ese email!"));
                    }
                }
            }}).register(data);
        } else {
            findAndDisplayErrors($(this).parent());
        }
        return false;
    });
}

function onLoginComplete (){
    $("#login-form input").each(function(){
        setInputRule($(this));
    });
    
    //LOGIN SUBMIT
    $("#login-form form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            var resp = Contabilidad.endPoint({async : true, success: function(resp){
                if(resp.result == "success"){
                    document.location.href = Contabilidad.private_home;
                } else if(resp.result == "failure") {
                    if(resp.reason == USER_NOT_FOUND){
                        $("#login-form .error").html(Contabilidad.tr("correo o contraseña incorrectos"));
                    }
                }
            }}).login(data);
        } else {
            findAndDisplayErrors($(this).parent());
        }
        return false;
    });
}

function onLoginStart(){
    $("#login-form").show();
}

function findAndDisplayErrors($form)
{
    var errors;
    $form.find(".input-error").each(function(){
        errors =  $(this).data("errors");
        if(errors.length){
            return false;
        }
    });
    $form.find(".error").html(errors[0].message).show();
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


