<?php 
/*
Plugin Name: Products Enquiry Form for WooCommerce 
Plugin URI: http://www.wlm.webcodingplace.com/
Author: Ansar Bhatti
Author URI: https://webcodingplace.com/
Text Domain: pef
Description: Renders an enquiry form in additional tab with all woocommerce products
License: GPL
Version: 1.0
*/

// exit if accessed directly
if( ! defined('ABSPATH' ) ){
    exit;
}

define( 'PEF_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define( 'PEF_URL',  untrailingslashit(plugin_dir_url( __FILE__ )) );


/* ======= plugin includes =========== */

 if( file_exists( dirname(__FILE__).'/inc/class/class.settings.php' )) include_once dirname(__FILE__).'/inc/class/class.settings.php';
 if( file_exists( dirname(__FILE__).'/inc/recaptcha.php' )) include_once dirname(__FILE__).'/inc/recaptcha.php';
 


class PEF_MAIN {

    private static $ins = null;
    
    function __construct(){

        if( !session_id() )
        {
            session_start();
        }

        add_action( 'admin_menu', array($this, 'pef_add_submenu_page') , 99 );

        // add custom tabs
        add_filter( 'woocommerce_product_tabs', array($this, 'woo_new_product_tab_inquiry'), 20, 1 );

        // enquriy form submition
        add_action( 'wp_ajax_user_send_message_for_product', array($this, 'user_send_message_for_product') );
        add_action( 'wp_ajax_nopriv_user_send_message_for_product', array($this, 'user_send_message_for_product') );
    }


    /* ----------------------
      loading template files
     ------------------------*/
    function pef_load_templates( $template_name, $vars = null) {

        if( $vars != null && is_array($vars) ){
            extract( $vars );
        };

        $template_path =  PEF_PATH . "/templates/{$template_name}";
        if( file_exists( $template_path ) ){
            require ( $template_path );
        } else {
            die( "Error while loading file {$template_path}" );
        }
    }

    /* ------------------------
      script load enquire form
    ---------------------------*/
    function load_dashboard_script(){

        // bootstrap file load
        wp_enqueue_style('pef-dashboard-bsrtp', PEF_URL."/css/bootstrap.min.css");
        wp_enqueue_style('pef-main', PEF_URL."/css/inquriy-form.css");
        wp_enqueue_script('pef-bsrp', PEF_URL."/js/bootstrap.min.js", array('jquery'), true);
        wp_enqueue_script('pef-main', PEF_URL."/js/main.js", array('jquery'), true);

         $pef_form = array(
          'ajax_url'   => admin_url( 'admin-ajax.php') ,
          'loading'    => PEF_URL.'/images/pef-loader.gif',
          'error_msg'  => 'Please remove above error before update',
        );
        // ajax load
        wp_localize_script( 'pef-main', 'pef_vars', $pef_form);
      
       
    }

    /* -----------------------------------------
     add the submenu page for settings
     -------------------------------------------*/
    function pef_add_submenu_page() { 
        add_submenu_page( 'woocommerce', 'Product Enquiry Form', 'Enquiry Form', 'manage_options', 'wp_form', array($this,
            'pef_options_page') );
    }

    /*
    *** 
    */

    function pef_options_page() {

        $this->pef_load_templates("form_settings.php");
    }


    // new form render
    function woo_new_product_tab_inquiry( $tabs ) {
    // Adds the new tab
        $tabs['inquiry_tab'] = array(
            'title'     => __( 'Inquiry Form', 'woocommerce' ),
            'priority'  => 50,
            'callback'  =>  array($this, 'woo_new_product_tab_content'),
        );
        return $tabs;
    }

    function woo_new_product_tab_content() {
      
        $this->render_inquriy_form();
    }

 
    /* -----------------------------------------
     This shortcode function use to display form 
     -------------------------------------------*/
    function render_inquriy_form(){
    	
    	$this->load_dashboard_script();
        ob_start();

        $member_template   = "inquriy_form.php";
        $template_vars      = array( "form" => $this );  
        $this->pef_load_templates( $member_template, $template_vars );
    }


    /* -----------------------------------------
     ajax base funtion get data and send email
     -------------------------------------------*/
    function user_send_message_for_product() {

        // check nonce for scurity
        if( ! $this->wpr_is_nonce_clear( 'pef_form_sumbit') )
            die('sorry for security reason');

        if( empty($_POST['user_email']) ) {

            $response = array('status'=>'error','message'=>__('Please write the email','pef'));
            wp_send_json( $response );
        }

        $post_email       = $_POST['user_email'];
        $user_email       = $this->get_user_email($post_email);
        $user_message     = $_POST['user_message'];



        // get email subject
        $email_subject  = PEF_Settings()->get_option('pef_email_subject') == '' ? ' Woocommerce Product' : 
                          PEF_Settings()->get_option('pef_email_subject');

        // success msg
        $success_msg    = PEF_Settings()->get_option('pef_form_success_msg');

        // error msg
        $error_msg      = PEF_Settings()->get_option('pef_form_error_msg');

       
        $admin_email = get_bloginfo('admin_email');

        if (wp_mail( $admin_email, $email_subject, $user_message, $user_email )) {
            $response = array('status'=>'success', 'message'=>__("Send Email Successfully! {$success_msg}", 'pef'));
            
        } else {

            $response = array('status'=>'error', 'message'=>__("{$error_msg}", 'pef'));
        }
        
        wp_send_json( $response );  

    }

    /* -----------------------------------------
    get user email into string
     -------------------------------------------*/
    function get_user_email($post_email){
        
        
        $headers[] = "From: {$post_email}";
        $headers[] = "Content-Type: text/html";
        $headers[] = "MIME-Version: 1.0\r\n";
        
        return apply_filters('wpr_email_header', $headers);
    }

    /* -----------------------------------------
    Checking nonce against actiong
     -------------------------------------------*/
    function wpr_is_nonce_clear( $action_name ) {

        $is_clear = true;
        if ( !wp_verify_nonce( $_POST['pef_nonce'], $action_name ) ) 
            $is_clear = false;

        return $is_clear;
    }

    public static function get_instance() {
          // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
     
}

// lets start plugin
add_action('plugins_loaded', 'pef_start');
function pef_start() {
    return  PEF_MAIN::get_instance();
}
if( is_admin() ) {
    PEF_Settings();
}