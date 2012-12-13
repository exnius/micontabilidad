$(document).ready(function(){
    $("#login-form form").submit(function(){
        var data = {};
        $(this).find("input").each(function(){
            data[$(this).attr("name")] = $(this).val();
        });
        var resp = Contabilidad.endPoint.login(data);
        if(resp.result == "success"){
            document.location.href = Contabilidad.private_home;
        } else if(resp.result == "failure") {
            if(resp.reason == "wrong authentication"){
                $("#login-form").append("<span>correo/password incorrectos</span>");
            }
        }
        return false;
    });
    
    $(".js-fancy-register").fancybox({
        'content' : $("#login-form"),
        'onStart' : onRegisterStart()
    });
});

function onRegisterStart(){
    $("#login-form").show();
}


