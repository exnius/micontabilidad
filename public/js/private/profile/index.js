$(document).ready(function(){
    //rules
    $("#profile-form form input[type='text']").each(function(){
        setInputRule($(this));
    });
    
    $("#profile-form form").submit(function(){
        if(Contabilidad.Validate.isValid($(this))){
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
    })
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
    Contabilidad.Validate.setRules($input, rules);
}