require('jquery-validation/dist/jquery.validate.min');
$(document).ready(function(){

    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;

    $(".next").click(function(){

        var form = $("#msform");
        form.validate({
            rules: {
                "registration_form[disclaimer]": {
                    required: true,
                },
            },
            errorLabelContainer: ".js-errors",
            messages: {
                "registration_form[disclaimer]": {
                    required: "Vous devez accepter les conditions d'utilisation avant de poursuivre.",
                },
            }
        });
        if (form.valid() === true){
            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();
        }
//Add Class Active
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

//show the next fieldset
        next_fs.show();

//hide the current fieldset with style
        current_fs.animate({opacity: 0}, {
            step: function(now) {

// for making fielset appear animation
                opacity = 1 - now;

                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                next_fs.css({'opacity': opacity});
            },
            duration: 600
        });
    });

    $(".previous").click(function(){

        current_fs = $(this).parent().parent();
        previous_fs = $(this).parent().parent().prev();

//Remove class active
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

//show the previous fieldset
        previous_fs.show();

//hide the current fieldset with style
        current_fs.animate({opacity: 0}, {
            step: function(now) {

// for making fieldset appear animation
                opacity = 1 - now;

                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                previous_fs.css({'opacity': opacity});
            },
            duration: 600
        });
    });

    $(".submit").click(function(){
        return false;
    })

});