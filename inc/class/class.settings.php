<?php
/**
 *  Setting Manager Class
 * 
 * 1- Rendering Settings
 * 2- Save Settings
 * 3- Get Settings
 * 
 */
 
class PEF_Settings {
    
    var $settings;
    var $setting_key;
    var $saved_settings;
    
    private static $ins = null;
    
    function __construct() {
        
        $this->settings = $this->pef_get_admin_setting();
        $this->setting_key = 'pef_settings';
        
        $this->saved_settings = $this->get_settings();
        
        add_action('wp_ajax_save_'.$this->setting_key, array($this, 'save_settings'));
    }
    
    public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    
    // Display Function
    public function display() {
        
        $this->loader_css();

        wp_enqueue_style('pef-bootstrap', PEF_URL."/css/bootstrap.min.css");
        wp_enqueue_style('pef-setting-css', PEF_URL."/css/pef-admin-setting.css");
        wp_enqueue_style('pef-settings-css', PEF_URL."/css/jquery-ui-css.css");
        
        wp_enqueue_script('pef-setting-js', PEF_URL."/js/pef-admin-setting.js", array('jquery' ,'jquery-ui-core', 'jquery-ui-tabs'), '1.0', true);

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        ?>
        <div class="pef_refresh_loader"></div>
        <div class="pef-settings-wrapper pef_setting_page_hide" style="display: none;">
            <form id="<?php echo esc_attr($this->setting_key); ?>_form">
                <input type="hidden" name="action" value="save_<?php echo esc_attr($this->setting_key) ?>">
                <div id="tabs">
                  <ul>
                      <?php foreach ($this-> settings as $tab_title => $data) {
                          $tab_id = sanitize_key($tab_title);
                          
                           echo '<li><a href="#'.esc_attr($tab_id).'">'.$tab_title.'</a></li>';
                      }
                      ?>
                      
                   </ul>
                    <?php foreach ($this-> settings as $tab_title => $data) {
                        
                        $tab_id = sanitize_key($tab_title);
                    ?>
                        <div id="<?php echo esc_attr($tab_id); ?>">
                            <table class="form-table tb-control">
                                <?php foreach ($data as $key => $field_data) {
                                    $type       = isset($field_data['type']) ? $field_data['type'] : '';
                                    $id         = isset($field_data['id']) ? $field_data['id'] : '';
                                    $advance_url = isset($field_data['pef_advance']) ? $field_data['pef_advance'] : '';

                                    $label      = isset($field_data['label']) ? $field_data['label'] : '';
                                    $desc       = isset($field_data['description']) ? $field_data['description'] : '';
                                    $default    = isset($field_data['default']) ? $field_data['default'] : '';
                                    // divide rows for heading
                                    $divider  = $type == 'divider' ? 'pef-divider-heading' : '';
                                    // pef_pa($type);
                                    $show_url = $id == 'pef_advance_redirect' ? 'pef-url-toggle':'';
                                    $hide_url = $advance_url == 'set_advance'? 'set_advance' : '';
                                
                                    ?>
                                        <tr 
                                            class="<?php echo esc_attr($divider); ?>" 
                                            data-hide-url ="<?php echo esc_attr($hide_url); ?>"
                                            data-show-url ="<?php echo esc_attr($show_url); ?>"
                                        >
                                        <?php if($label) { ?>
                                            
                                            <td class="pef-label-text"><?php echo $label; ?></td>
                                        <?php } ?>
                                            <td colspan="2"><?php echo $this->input($type, $id, $default); ?></td>
                                            <td class = "pef-desc-text"><?php echo $desc; ?></td>
                                        </tr>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>

                </div>
                <span class="pef_sub_st_control">
                    <input type="submit" class="btn btn-primary">
                     <div class="pef_save_alert pef-alert-display"><?php _e('Settings Saved' , 'wp-registration'); ?></div>
                     <span class="pef-spinner"></span>
                </span>
            </form>
        </div>
        <?php
    }
    
    // Render input control
    function input( $type, $id, $default, $options="") {
        
        $input_html = '';
        $name  = $this->setting_key.'['.$id.']';
        $value = ($this->get_option($id) == '') ? $default : $this->get_option($id);

        switch( $type ) {
        
            case 'text':
                
                $input_html .= '<input class="form-control pef-text-option" name="'.esc_attr($name). '" type="text" id="'.esc_attr($id).'" value="'.esc_attr($value).'">';
                break;
            case 'checkbox':
                
                $input_html .= '<input  name="'.esc_attr($name).'" type="checkbox" id="'.esc_attr($id).'" value="on" '.checked($value,'on', false).'>';
                $input_html .='<label for="'.esc_attr($id).'" class="pef-chk-option">'.esc_html__( 'Yes', 'wp-registration' ).'</label>';
                break;
            case 'pef_color':
                
                $input_html .= '<input name="'.esc_attr($name).'" class="wp-color" id="'.esc_attr($id).'" value="'.esc_attr($value).'">';
                break;
        }
        
        return $input_html;
    }

     /*--------------------------------------
     This function render all setting array 
    ----------------------------------------*/
    function pef_get_admin_setting() {

        $pef_setting_options = array(
            'Form Settings' =>  array(
                array(
                    'type'         => 'text',
                    'id'           => 'pef_form_header',
                    'label'        => __("Form Header", 'pe_form'),
                    'description'  => __('Change the form header.', 'pe_form'),
                ),
                array(
                    'type'         => 'text',
                    'id'           => 'pef_email_subject',
                    'label'        => __("Email Subject", 'pe_form'),
                    'description'  => __('Write the subject of email.', 'pe_form'),
                ),
                array(
                    'type'         => 'text',
                    'id'           => 'pef_form_success_msg',
                    'label'        => __("Success Message", 'pe_form'),
                    'description'  => __('Write success msg on form submition.', 'pe_form'),
                ),
                array(
                    'type'         => 'text',
                    'id'           => 'pef_form_error_msg',
                    'label'        => __("Error Message", 'pe_form'),
                    'description'  => __('Write error msg on form submition.', 'pe_form'),
                ),
                array(
                    'type'         => 'pef_color',
                    'id'           => 'pef_form_clr',
                    'label'        => __("Background Color", 'pe_form'),
                    'description'  => __('Select color for enquriy form.', 'pe_form'),
                ),
                array(
                    'type'         => 'pef_color',
                    'id'           => 'pef_form_header_clr',
                    'label'        => __("Form Header Color", 'pe_form'),
                    'description'  => __('Select color for change form header.', 'pe_form'),
                ),
                array(
                    'type'         => 'pef_color',
                    'id'           => 'pef_form_btn',
                    'label'        => __("Button Color", 'pe_form'),
                    'description'  => __('Select color for enquriy form Button.', 'pe_form'),
                ),
                
            ),
        ); 
        
        return apply_filters( 'pef_options', $pef_setting_options);
    }
    
    function loader_css(){
        ?>
        <style type="text/css">
        .pef_refresh_loader {
            border: 6px solid #f3f3f3;
              border-radius: 60%;
              border-top: 9px solid #3498db;
              width: 60px;
              height: 60px;
              -webkit-animation: spin 1s linear infinite; /* Safari */
              animation: spin 1s linear infinite;
            
            margin-left: 36%;
            margin-top: 20%;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
        <?php
    }
    
    // Saving settings
    function save_settings() {
        
       if( !isset($_POST[$this->setting_key]) ) 
            wp_die('No Data Found');
             
        $settings_data = $_POST[$this->setting_key];

        
        update_option($this->setting_key, $settings_data);
        wp_die( __("Settings updated successfully", "pef") );
    }
    
    // Get all settings from option
    function get_settings() {
        
        $settings = get_option($this->setting_key);
        return $settings;
    }
    
    // Get option value
    function get_option($id) {
        
        if( isset($this->saved_settings[$id]) ) {
            return $this->saved_settings[$id];
        }
        
        return '';
    }

    // Set option value
    function set_option($key, $value) {
        
        $this->saved_settings[$key] = $value;
        update_option($this->setting_key, $this->saved_settings);
    }
}

function PEF_Settings() {
    
    return PEF_Settings::get_instance();
}