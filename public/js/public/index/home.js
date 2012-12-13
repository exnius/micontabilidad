$(document).ready(function(){
    
    const USER_NOT_FOUND = "wrong authentication";
    const EMAIL_ALREADY_REGISTERED = "email already registered";
    
    //LOGIN SUBMIT
    $("#login-form form").submit(function(){
        var data = {};
        $(this).find("input").each(function(){
            data[$(this).attr("name")] = $(this).val();
        });
        var resp = Contabilidad.endPoint.login(data);
        if(resp.result == "success"){
            document.location.href = Contabilidad.private_home;
        } else if(resp.result == "failure") {
            if(resp.reason == USER_NOT_FOUND){
                $("#login-form .error").html(Contabilidad.tr("correo/password incorrectos"));
            }
        }
        return false;
    });
    
    //REGISTER SUBMIT
    $("#register-form form").submit(function(){
        var data = {};
        $(this).find("input").each(function(){
            data[$(this).attr("name")] = $(this).val();
        });
        var resp = Contabilidad.endPoint.register(data);
        if(resp.result == "success"){
            document.location.href = Contabilidad.private_home;
        } else if(resp.result == "failure") {
            if(resp.reason == EMAIL_ALREADY_REGISTERED){
                $("#register-form .error").html(Contabilidad.tr("Ya existe usuario con ese email!"));
            }
        }
        return false;
    });
    
    $(".js-fancy-login").fancybox({
        'content' : $("#login-form"),
        'onStart' : onLoginStart()
    });
    
    $(".js-fancy-register").fancybox({
        'content' : $("#register-form"),
        'onStart' : onRegisterStart()
    });
});

function onRegisterStart(){
    $("#register-form").show();
}

function onLoginStart(){
    $("#login-form").show();
}


