$(document).ready(function(){
    
    //CREATE ACCOUNT FANCYBOX
    $("#js-fancy-create-account").click(function(){
        QHelpers.account.showBalancePopup($(this));
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
        } else if ($(event.target).hasClass("edit-account")){
            var id = $(event.target).parent().attr("data-id");
            var nAddFrag = document.createDocumentFragment();
            if(!$(event.target).data("el")){
                var el = document.getElementById("create-account-form");
                $(event.target).data("el", el);
            }
            nAddFrag.appendChild($(event.target).data("el"));
            var $div = $("<div>").append(nAddFrag);
            Contabilidad.getEndPoint({async : true, success: function(account){
                if (account){
                    QHelpers.account.showBalancePopup($(event.target), account)
                }
            }}).getAccountById(id);
        }
    });
});

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
        errors = $(this).data("errors");
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