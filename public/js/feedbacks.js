$(document).ready(function(){
    var el = document.getElementById("feedback-tpl").innerHTML;
    var $div = $("<div>").append(el);
    $(".wv-js-feedbacks").click(function(){
        $.fancybox({
            "content": $div,
            "onStart" : onFeedbackStart($div),
            "onClean" : $div.find("textarea").val("")
        });
        return false;
    });
});

function onFeedbackStart($div){
    Contabilidad.Validate.setRules($div.find("textarea"), {required: true});
    $div.find('#feedback-chars-label label.charsLeft').html(140);
    
    $div.find("textarea#feedback-comments").keyup(function(){
        var len = $(this).val().length;
        if (len > 140) {
            this.value = this.value.substring(0, 140);
            len = this.value.length;
        }
        $div.find('#feedback-chars-label label.charsLeft').text(140 - len);
    }).keydown(function(){
        var len = $(this).val().length;
        $div.find('#feedback-chars-label label.charsLeft').text(140 - len);
    });
    
    $div.find("form").submit(function(){
        Contabilidad.Validate.clean($div);
        $div.find(".response").removeClass("*").html("");
        if(Contabilidad.Validate.isValid($(this), "textarea")){
            $.fancybox({
                "content" : Contabilidad.tr("Gracias por tus comentarios!")
            });
            Contabilidad.getEndPoint({async : true, success: function(resp){
            }}).sendFeedback($div.find("textarea").val());
        } else {
            findAndDisplayErrors($div);
        }
        return false;
    });
}