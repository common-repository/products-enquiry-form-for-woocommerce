"use strict";
jQuery(function($){

    $('#enquiry_form').find('.pef-form-alert').hide();

    //send the admin message from user profile
    $("#enquiry_form").submit(function(e){
        e.preventDefault();

        var data = $(this).serialize();
       
        $.post(pef_vars.ajax_url, data, function(resp) {
            jQuery(".pef-btn-loader").html('<img src="' + pef_vars.loading + '">').css('border-left','none').show();
            if (resp.status == 'error' && resp.message !='') {
            console.log('resp');

                $('#enquiry_form').find('.pef-form-alert').show().html(resp.message).css({"background-color": '#f4433685',
                                                                                      "border-left" : '4px solid #FF5722',
                                                                                      "border-right": '4px solid #FF5722'});
            }
            else if (resp.message !='') {
            $('#enquiry_form').find('.pef-form-alert').show().html(resp.message).css({"background-color": '#70c576',
                                                                                      "border-left" : "4px solid #4CAF50"});
            }
            setTimeout(function(){
                $('.pef-btn-loader').hide();
                location.reload();
            }, 3000)
            setTimeout(function(){
                $('.pef-form-alert').hide();
            }, 7000);
         
        });
 
    });
});    