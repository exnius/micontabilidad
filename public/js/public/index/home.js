const USER_NOT_FOUND = "wrong authentication";
const EMAIL_ALREADY_REGISTERED = "email already registered";

$(document).ready(function(){
    
    //LOGIN FANCYBOX
    $(".js-fancy-login").click(function(){
        var nAddFrag = document.createDocumentFragment();
        if(!$(this).data("el")){
            var el = document.getElementById("login-form");
            $(this).data("el", el);
        }
        nAddFrag.appendChild($(this).data("el"));
        var $div = $("<div>").append(nAddFrag);
        
        $.fancybox({
            'content' : $div,
            'onStart' : onLoginStart($div),
            'onCleanup' : function(){
                this.form = document.getElementById("login-form");
            },
            'onClosed' : function(){
                onClose(this.form);
            },
            'onComplete' : function(){
                onLoginComplete($div);
            }
        });
        return false;
    });
    
    //REGISTER FANCYBOX
    $(".js-fancy-register").click(function(){
        var nAddFrag = document.createDocumentFragment();
        if(!$(this).data("el")){
            var el = document.getElementById("register-form");
            $(this).data("el", el);
        }
        nAddFrag.appendChild($(this).data("el"));
        var $div = $("<div>").append(nAddFrag);
        
        $.fancybox({
            'content' : $div,
            'onStart' : function(){
                onRegisterStart($div)
            },
            'onCleanup' : function(){
                this.form = document.getElementById("register-form");
            },
            'onClosed' : function(){
                onClose(this.form);
            }
        });
        
        return false;
    });
});

/*************************************
 **********REGISTER METHODS***********
 *************************************/

function onRegisterStart ($div){
    $div.find("#register-form").show();
    
    $div.find("#register-form input").each(function(){
        setInputRule($(this));
    });
    
    //REGISTER SUBMIT
    $div.find("form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            var resp = Contabilidad.getEndPoint({async: true, success: function(resp){
                if(resp.result == "success"){
                    document.location.href = Contabilidad.private_home;
                } else if(resp.result == "failure") {
                    if(resp.reason == EMAIL_ALREADY_REGISTERED){
                        $div.find(".response")
                        .addClass("error")
                        .html(Contabilidad.tr("Ya existe usuario con ese email!"));
                    }
                }
            }}).register(data);
        } else {
            findAndDisplayErrors($(this).parent());
        }
        return false;
    });
}


/*************************************
 **********LOGIN METHODS**************
 *************************************/

function onLoginStart($div){
    $div.find("#login-form").show();
    
    $div.find("#login-form input").each(function(){
        setInputRule($(this));
    });
    
    //LOGIN SUBMIT
    $div.find("form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            var resp = Contabilidad.getEndPoint({async : true, success: function(resp){
                if(resp.result == "success"){
                    document.location.href = Contabilidad.private_home;
                } else if(resp.result == "failure") {
                    if(resp.reason == USER_NOT_FOUND){
                        $div.find(".response")
                        .addClass("error")
                        .html(Contabilidad.tr("correo o contraseña incorrectos"));
                    }
                }
            }}).login(data);
        } else {
            findAndDisplayErrors($div.find("#login-form"));
        }
        return false;
    });
}

function onLoginComplete ($div){
    //RECOVER PASSWORD FANCYBOX
    $div.find("#js-fancy-recover-password").click(function(){
        var nAddFrag = document.createDocumentFragment();
        if(!$(this).data("el")){
            var el = document.getElementById("recover-password-form");
            $(this).data("el", el);
        }
        nAddFrag.appendChild($(this).data("el"));
        var $div = $("<div>").append(nAddFrag);
        
        $.fancybox({
            'content' : $div,
            'onStart' : function(){
                onRecoverStart($div)
            },
            'onCleanup' : function(){
                this.form = document.getElementById("recover-password-form");
            },
            'onClosed' : function(){
                onClose(this.form);
            }
        });
    });
}


/*************************************
 *******RECOVER PASS METHODS**********
 *************************************/

function onRecoverStart($div){
    $div.find("#recover-password-form").show();
    
    $div.find("#recover-password-form input").each(function(){
        setInputRule($(this));
    });
    
    //LOGIN SUBMIT
    $div.find("#recover-password-form form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
            var data = {};
            $(this).find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            var resp = Contabilidad.getEndPoint({async : true, success: function(resp){
                if(resp.result == "success"){
                    $div.find("#recover-password-form .response")
                    .addClass("success")
                    .html(Contabilidad.tr("Da click en el link que te enviamos por email"));
                } else if(resp.result == "failure") {
                    if(resp.reason == USER_NOT_FOUND){
                        $div.find("#recover-password-form .response")
                        .addClass("error")
                        .html(Contabilidad.tr("No existe usuario con ese email"));
                    }
                }
            }}).recoverPassword(data);
        } else {
            findAndDisplayErrors($(this).parent());
        }
        return false;
    });
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

/*************************************
 **********AUTH METHODS***********
 *************************************/

function OnLoadGApiCallback(){
    gapi.client.load('oauth2', 'v2', function() { });
    gapi.client.setApiKey(GOOGLE_API);
    var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
    console.info("load");
    window.setTimeout(checkAuth,1);
}

function checkAuth() {
    var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
    gapi.auth.authorize({client_id: GOOGLE_API, scope: scopes, immediate: true}, handleGAuthResult);
}

function handleGAuthResult(authResult){
    if (authResult && !authResult.error) {
        loginByGoogle(authResult);
    } else {
        $('.js-google-login').each(function(){
            this.onclick = clickOnGoogleLogin;
        });
    }
}

function clickOnGoogleLogin(event){
    var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
    gapi.auth.authorize({client_id: GOOGLE_API, scope: scopes, immediate: false}, function(resp){loginByGoogle(resp)});
    return false;
}

function loginByGoogle(authResult){
    var request = gapi.client.request({
        'path': '/oauth2/v1/userinfo',
        'params': {'access_token': authResult.access_token,
                    'token_type' : 'Bearer',
                    'expires_in' :3600}
    });
    request.execute(function(resp) {
        console.info(resp);//datos de google
    });
}