$(document).ready(function(){
    
    $('#add-incomes').qtip({
        overwrite: true,
        content: {
            text: function(api) {
                $('#create-transaction-form').show();
                return $('#create-transaction-form')
            }
        },
        events: {
            visible: function(event, api) {
                $("#create-transaction-form input[type='text']").each(function(){
                    setInputRule($(this));
                });
                $('#create-transaction-form form').submit(function(){
                    Contabilidad.Validate.clean($('#create-transaction-form'));
                    $('#create-transaction-form .response')
                    .html("").removeClass("*").addClass("response");
                    if(Contabilidad.Validate.isValid($(this))){
                        var data = {};
                        data.name = $("#create-transaction-form input[name='name']").val();
                        data.value = $("#create-transaction-form input[name='value']").val();
//                        Contabilidad.getEndPoint({async : true, success: function(resp){
//                            var output = Mustache.render($("#transaction-row-tpl").html(), resp.transaction);
//                            $("#transactions-container").prepend(output);
//                            $('#add-incomes').qtip('hide');
////                            $.fancybox.close();
//                        }}).createTransaction(data);
                    } else {
                        findAndDisplayErrors($("#create-transaction-form").parent());
                    }
                    return false;
                });
                
                $("#create-transaction-form input[name='value']").keypress(function(evt){
                    var charCode = (evt.which) ? evt.which : event.keyCode
                    if ((charCode > 31 && (charCode < 48 || charCode > 57)) &&
                    (charCode != 44 && charCode != 46)){
                        return false;
                    }
                    return true;
                }).keyup(function(evt){
                    var charCode = (evt.which) ? evt.which : event.keyCode
                    if ((charCode > 36 && charCode < 40) || charCode==16){
                        return true;
                    } else {
                        var value = this.value.replace(/\./g, '');
                        value = value.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
                        $(this).val(value);
                    }
                });
                
            }, 
            hidden: function(event, api) {
                $('#create-transaction-form .response')
                .html("").removeClass("*").addClass("response");
                $("#create-transaction-form input[type='text']").each(function(){
                    $(this).val("");
                });
            }
        },
        show: {
            event: 'click'
        },
        hide: {
            event: 'unfocus'
        }
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
    if($input.hasClass("money")){rules.money = true;}
    if($input.hasClass("is_email")){rules.email = true;}
    if($input.attr("type") == "password"){rules.password = true;}
    if($input.attr("is_equal_to")){rules.equalsTo = $input.parent().find("input[name='" + $input.attr("is_equal_to") + "']");}
    Contabilidad.Validate.setRules($input, rules);
}

