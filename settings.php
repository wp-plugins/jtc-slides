<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('JTC_Slides_Settings'))
{
	class JTC_Slides_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct
		
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register your plugin's settings
        	register_setting('jtc_slides-group', 'setting_a');
        	register_setting('jtc_slides-group', 'setting_b');

        	// add your settings section
        	add_settings_section(
        	    'jtc_slides-section', 
        	    'JTC Slides Settings', 
        	    array(&$this, 'settings_section_jtc_slides'), 
        	    'jtc_slides'
        	);
        	
        	// add your setting's fields
            add_settings_field(
                'jtc_slides-setting_a', 
                'Setting A', 
                array(&$this, 'settings_field_input_text'), 
                'jtc_slides', 
                'jtc_slides-section',
                array(
                    'field' => 'setting_a'
                )
            );
            add_settings_field(
                'jtc_slides-setting_b', 
                'Setting B', 
                array(&$this, 'settings_field_input_text'), 
                'jtc_slides', 
                'jtc_slides-section',
                array(
                    'field' => 'setting_b'
                )
            );
            // Possibly do additional admin_init tasks
        } // END public static function activate
        
        public function settings_section_jtc_slides()
        {
            // Think of this as help text for the section.
            echo 'These settings do things for the WP Plugin Template.';
        }
        
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)
        
        /**
         * add a menu
         */		
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'JTC Slides Settings', 
        	    'JTC Slides', 
        	    'manage_options', 
        	    'jtc_slides', 
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()
    
        /**
         * Menu Callback
         */		
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class WP_Plugin_Template_Settings
} // END if(!class_exists('WP_Plugin_Template_Settings'))
