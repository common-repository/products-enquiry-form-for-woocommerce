"use strict";
jQuery(function($){


    $(window).load(function() {
        // Animate loader off screen
        $(".pef_refresh_loader").hide();
        $('.pef_setting_page_hide').show();
    });

    // tabs system for pef settings
    $('.wp-color').wpColorPicker();
        $( "#tabs" ).tabs();

    // ajax callback function for saved all pef settings
    $('.pef_sub_st_control').find('.pef-spinner').hide(); //@today_work
    $('#pef_settings_form').on('submit',function(e){
        e.preventDefault();

        $('.pef_sub_st_control').find('.pef-spinner').show(); //@today_work
        $(this).find('.pef_sub_st_control input').prop('disabled', true); //work

        var data = $(this).serialize();
        
        $.post(ajaxurl, data, function(response){
            $('.pef_sub_st_control').find('.pef-spinner').hide(); //@today_work
            $('.pef_save_alert').removeClass('alert_display');
            window.location.reload();
            
        });
    });

    $('[data-hide-url ="set_advance"]').hide();
    $(document).on('click', '[data-show-url ="pef-url-toggle"]', function(e) {
        e.preventDefault();
        $('[data-hide-url="set_advance"]').slideToggle(500);
    // $('[data-advance="set_advance"]').show();
    });


    // select2 control for pef settings
    $('.pef-select2').select2({
        placeholder: 'Select',
        width:"65%",
    });

    $(".gn_roles").select2({
        placeholder: "Select",
        // allowClear: true,
        width:"65%",
        // multiple:true
    });

    $("Select.pef_op_select").select2({
        placeholder: "Select",
        allowClear: true,
        width:"65%",
    });
});