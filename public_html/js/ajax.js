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
                $($clicked).find(".profile-usertitle-name").text(response[0]);
                $($clicked).find(".profile-usertitle-job").text(response[1]);
                $($clicked).find(".hover-desc").text(response[3]);
                
                if (response[2] != null) {
                    $($clicked).find(".userpic-inner").attr('src', 'userpics/'+response[2]);
                } else {
                    $($clicked).find(".userpic-inner").attr('src', 'media/profile-placeholder.png');
                }
            }
        });           
    //$(this).find(".userpic-inner").attr('src', 'media/profile-placeholder.png');
    $(this).children().fadeTo(100, 1);
    }
});
