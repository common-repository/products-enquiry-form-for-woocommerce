<?php
/**
 * Settings
 **/

    // not run if accessed directly
    if( ! defined("ABSPATH" ) )
         die("Not Allewed");

    $form_header  = PEF_Settings()->get_option('pef_form_header') == '' ? ' SEND ENQUIRY FOR PRODUCT' :
                   PEF_Settings()->get_option('pef_form_header') ;

    $form_clr     = PEF_Settings()->get_option('pef_form_clr') == '' ? '#ffffff' : PEF_Settings()->get_option('pef_form_clr');
    $form_btn_clr = PEF_Settings()->get_option('pef_form_btn') == '' ? '#03a9f482' : PEF_Settings()->get_option('pef_form_btn');
    $header_clr   = PEF_Settings()->get_option('pef_form_header_clr') == '' ? '#76c9e2' : 
                    PEF_Settings()->get_option('pef_form_header_clr');
?>

<div class="form-container" style="background-color:<?php echo esc_attr($form_clr); ?>;">
    <div class="panel-heading pef-form-header" style="background-color:<?php echo esc_attr($header_clr); ?>;">
        <h2>
            <?php echo esc_html($form_header); ?>
        </h2>
    </div>
    <div class="panel-body">
        <form id="enquiry_form">
            <input type="hidden" name="action" value="user_send_message_for_product" >

                <?php wp_nonce_field( 'pef_form_sumbit', 'pef_nonce' ) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Your Name:' , 'pe-form') ?></label>
                        <input id="name" type="text" name="first_name" class="form-control input-lg" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Email Address:' , 'pe-form') ?></label>
                        <input id="email" type="email" name="user_email" class="form-control input-lg" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label><?php _e('Message:' , 'pe-form') ?></label>
                        <textarea id="message" name="user_message" rows="2" class="form-control input-lg" required></textarea>
                    </div>
                </div>
            </div>
                <span class="pef-btn-loader"></span>
                <input class="form-btn btn-info btn-block" style="background-color:<?php echo esc_attr($form_btn_clr); ?>;" type="submit" value="Send">   
   
                <div class="pef-form-alert"></div>
        </form>
    </div>
</div>