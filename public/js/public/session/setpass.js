$(document).ready(function(){
    $("#set-password-form input[type='password']").each(function(){
        setInputRule($(this));
    });
    
    $("#set-password-form form").submit(function(){
        $("#set-password-form .response").removeClass("*").html("").hide();
        if(Contabilidad.Validate.isValid($(this), "input")){
            var data = {
                new_pass : $(this).find("input[name='new_pass']").val(),
                token : Contabilidad.getURLParameter("token"),
                email : Contabilidad.getURLParameter("email").replace("%40", "@")
            };
            Contabilidad.getEndPoint({async : true, success: function(resp){
                if(resp.result == "failure"){
                    var msg = "";
                    if(resp.reason == "WRONG_USER"){
                        msg = Contabilidad.tr("Lo sentimos, no existe un usuario relacionado a este email '" + data.email + "'");
                    } else if(resp.reason == "WRONG_TOKEN"){
                        msg = Contabilidad.tr("Lo sentimos, el token ya fue usado");
                    } else {
                        msg = Contabilidad.tr("Lo sentimos, no podemos cambiar tu password. Intentalo mas tarde.");
                    }
                    $("#set-password-form .response").addClass("error").html(msg).show();
                } else {
                    document.location.href = Contabilidad.private_home;
                }
            }}).setPassword(data);
        } else {
            findAndDisplayErrors($(this).parent());
        }
        return false;
    });
});

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