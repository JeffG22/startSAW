"use strict";
//Shows/hides profiles
$(document).click(function() { 
        $('.proposer-name .profile-overlay').fadeOut(200);      
});
$(".profile-overlay").click(function(e){
    e.stopPropagation();
});
$(".proposer-name").click(function(e) {
    e.stopPropagation();
    var $clicked = this;
    var $id = $(this).parents(".card-body").find(".proposal_id").prop('value');
    if(typeof $id != "undefined") {
        $.ajax({
            type: 'POST',
            url: 'php/ajax_profile.php',
            data: {
                id : $id
            },
            dataType: 'json',
            success: function(response) {
            // We get the element having id of display_info and put the response inside it
                $($clicked).find(".profile-usertitle-name").text(response['display_name']);
                var $type = (response['type'] == 'person') ? "Volontario" : "Associazione";
                $($clicked).find(".profile-usertitle-job").text($type);
                $($clicked).find(".hover-desc").text(response['description']);

                if (response['picture'] != "" && response['picture'] != null) {
                    $($clicked).find(".userpic-inner").attr("src", response['picture']);
                }
            }
        });       
    $(this).children().delay(300).fadeTo(100, 1);
    }
});
