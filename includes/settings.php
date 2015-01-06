<?php
/**
 * Caldera Easy Rewrites Setting.
 *
 * @package   Caldera_Easy_Rewrites
 * @author    CalderaWP <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 CalderaWP <david@digilab.co.za>
 */

/**
 * Plugin class.
 * @package Caldera_Easy_Rewrites
 * @author  CalderaWP <david@digilab.co.za>
 */
class Settings_Caldera_Easy_Rewrites extends Caldera_Easy_Rewrites{


	/**
	 * Start up
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );
		// save config
		add_action( 'wp_ajax_cew_save_config', array( $this, 'save_config') );
		// rebuild rules
		add_action( 'wp_ajax_cew_rebuild_rules', array( $this, 'rebuild_rules') );
		
	}


	/**
	 * rebuilds rules
	 */
	public function rebuild_rules(){
		flush_rewrite_rules();
	}
	/**
	 * saves a config
	 */
	public function save_config(){

		if( empty( $_POST['caldera-easy-rewrites-setup'] ) || !wp_verify_nonce( $_POST['caldera-easy-rewrites-setup'], 'caldera-easy-rewrites' ) ){
			if( empty( $_POST['config'] ) ){
				return;
			}
		}
		
		// reset rewrites

		if( !empty( $_POST['caldera-easy-rewrites-setup'] ) && empty( $_POST['config'] ) ){
			$config = stripslashes_deep( $_POST );
			$config = $this->add_sanitization_and_validation( $config );
			update_option( '_caldera_easy_rewrites', $config );
			flush_rewrite_rules();
			wp_redirect( '?page=caldera_easy_rewrites&updated=true' );
			exit;
		}

		if( !empty( $_POST['config'] ) ){
			$config = json_decode( stripslashes_deep( $_POST['config'] ), true );
			$config = $this->add_sanitization_and_validation( $config );
			if(	wp_verify_nonce( $config['caldera-easy-rewrites-setup'], 'caldera-easy-rewrites' ) ){
				update_option( '_caldera_easy_rewrites', $config );
				flush_rewrite_rules();
				wp_send_json_success( $config );
			}
		}

		// nope
		wp_send_json_error( $config );

	}

	/**
	 * Adds the filter for sanization and/ or validation of each setting when saving.
	 *
	 * @param array $config Data being saved
	 *
	 * @return array
	 */
	protected function add_sanitization_and_validation( $config ) {
		foreach( $config as $setting => $value ) {
			if ( ! in_array( $setting, $this->internal_config_fields() ) ) {
				include_once( dirname( __FILE__ ) . '/sanatize.php' );
				$filtered = Settings_Caldera_Easy_Rewrites_Sanitize::apply_sanitization_and_validation( $setting, $value, $config );
				$config = $filtered;
			}

		}

		return $config;

	}

	/**
	 * Array of "internal" fields not to mess with
	 *
	 * @return array
	 */
	protected function internal_config_fields() {
		return array( 'hobbes-syncs-setup', '_wp_http_referer', 'id', '_current_tab' );
	}


	

	/**
	 * Add options page
	 */
	public function add_settings_pages(){
		// This page will be under "Settings"
		
	
			$this->plugin_screen_hook_suffix['caldera_easy_rewrites'] =  add_submenu_page( 'tools.php', __( 'Caldera Easy Rewrites', $this->plugin_slug ), __( 'Easy Rewrites', $this->plugin_slug ), 'manage_options', 'caldera_easy_rewrites', array( $this, 'create_admin_page' ) );
			add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix['caldera_easy_rewrites'], array( $this, 'enqueue_admin_stylescripts' ) );


	}


	/**
	 * Options page callback
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		include CEW_PATH .'includes/edit.php';

		// php based script include
		if( file_exists( CEW_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
				include CEW_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}


}

if( is_admin() )
	$settings_caldera_easy_rewrites = new Settings_Caldera_Easy_Rewrites();
