$(document).ready(function(){
    //rules
    $("#profile-form form input[type='text']").each(function(){
        setInputRule($(this));
    });
    
    $("#password-form input[type='password']").each(function(){
        setInputRule($(this));
    });
    
    $("#profile-form form").submit(function(){
        if(Contabilidad.Validate.isValid($(this), "input[type='text']")){
            var data = {};
            $(this).find("input,select").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            data.gender = $('input[name=gender]:checked').val();
            if(isUserFormDirty(data)){
                Contabilidad.getEndPoint({async : true, success: function(resp){
                    Contabilidad.user = resp.user;
                    alert("edited");
                }}).editUser(Contabilidad.user.id, data);
            }
        } else {
            findAndDisplayErrors($("#profile-form").parent());
        }
        return false;
    });
    
    $("#display-password-form").click(function(){
        if(!$("#password-form").is(":visible")) $("#password-form input[type='password']").val("");
        $("#password-form").animate({
            left: '+=50',
            height: 'toggle'
            }, 500, function() {
            // Animation complete.
        });
        return false;
    });
    
    $("#hide-password-form").click(function(){
        $("#password-form").animate({
            left: '+=50',
            height: 'toggle'
            }, 500, function() {
            // Animation complete.
        });
        return false;
    });
    
    var submitPassForm = function($form){
        $form.find(".password-response")
        .removeClass("*").html("").hide();
        if(Contabilidad.Validate.isValid($form, "input[type='password']")){
            var data = {};
            $form.find("input").each(function(){
                data[$(this).attr("name")] = $(this).val();
            });
            Contabilidad.getEndPoint({async : true, success: function(resp){
                if(resp.result == "failure"){
                    if(resp.reason == 'WRONG_PASSWORD'){
                        $form.find(".password-response")
                        .addClass("error")
                        .html(Contabilidad.tr("Tu contraseña actual es incorecta")).show();
                    }
                } else {
                    alert("your password was edited");
                }
            }}).editPassword(Contabilidad.user.id, data);
        } else {
            findAndDisplayEditPasswordErrors($form);
        }
        return false;
    }
    $("#password-form").keypress(function(e){
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            submitPassForm($("#password-form"));
        }
    });
});

function isUserFormDirty(data)
{
    var user = Contabilidad.user;
    var isDirty = false;
    for (var field in data ){
        if(typeof(user[field]) == "undefined") {
            delete data[field];
            continue;
        }
        if(data[field] != user[field]){
            isDirty = true;
        }
    }
    return isDirty;
}

function uploadAvatar(fileObj){
    var par = window.document;
    var frm = fileObj.form;
    
    $("#avatar-response").hide();

    // create new iframe
    var new_iframe = par.createElement('iframe');
    new_iframe.src = BASE_URL + "/private/profile/iframe";
    new_iframe.frameBorder = '0';
    new_iframe.scrolling = 'no';
    new_iframe.marginHeight = '0';
    new_iframe.marginWidth = '0';
    new_iframe.style.height = '75px';
    new_iframe.style.width = '500px';

    //hide old iframe
    var iframes = $("#iframe_container iframe");
    var iframe = iframes[iframes.length - 1];
    iframe.style.display = 'none';
    //append the new iframe
    $("#iframe_container").append(new_iframe);
    
    iframe.id = 'old-iframe';

    // send
    frm.submit();
}

function setUploadedImage(resp){
    if(resp.response == "success"){
        $("#avatar").attr("src", resp.url);
    } else {
        $("#avatar-response").removeClass("*")
        .addClass("error")
        .html(Contabilidad.tr("Lo sentimos, ocurrio un problema al subir tu imagen. Intenta con otra!"))
        .show();
    }
    var iframe = $("#iframe_container #old-iframe");
    $(iframe).remove();
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

function findAndDisplayEditPasswordErrors($form)
{
    var errors;
    $form.find(".input-error").each(function(){
        errors =  $(this).data("errors");
        if(errors.length){
            return;
        }
        return;
    });
    $form.find(".password-response").html(errors[0].message).show();
}

//set rules to an input
function setInputRule($input){
    if($input.attr("type") == "submit") return;
    //validation rulesc
    var rules = {required : $input.hasClass("required")};
    if($input.hasClass("is_email")){rules.email = true;}
    Contabilidad.Validate.setRules($input, rules);
}