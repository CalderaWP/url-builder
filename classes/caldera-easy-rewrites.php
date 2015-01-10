<?php
/**
 * Caldera Easy Rewrites.
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
class Caldera_Easy_Rewrites {

	/**
	 * @var      string
	 */
	protected $plugin_slug = 'caldera-easy-rewrites';
	/**
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// setup rewrite rules
		add_action( 'init', array( $this, 'define_rewrites' ), 100 );
		
		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		if ( is_admin() ) {
			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );
		}

	}


	/**
	 * Define the rewrites.
	 *
	 * @since 0.1.0
	 */
	public static function define_rewrites(){
		global $post;

		require_once( CEW_PATH . '/classes/magic-slugs.php' );

		//flush_rewrite_rules();
		$rules = get_option( '_caldera_easy_rewrites' );

		if( empty( $rules['rewrite'] ) ){
			return;
		}
		// start working on em.
		global $wp_rewrite;


		foreach( $rules['rewrite'] as $rule ){


			$new_rule_path = array( $rule['slug'] );
			if( !empty( $rule['segment'] ) ){
				foreach( $rule['segment'] as $segment_number => $segment ){
					$segment = Caldera_Easy_Rewrites_Magic_Slug::maybe_do_magic_slug( $segment[ 'path' ], $segment_number, $rule[ 'content_type' ], true, $post );
					$new_rule_path[] = $segment;
				}
			}

			$new_rule = implode( '/', $new_rule_path );


			foreach( $wp_rewrite->extra_rules_top as $rewrite_rule=>$rule_struct ){
				if( substr( $rewrite_rule, 0, strlen( $rules['content_types'][$rule['content_type']]['slug'] ) ) === $rules['content_types'][$rule['content_type']]['slug'] ){
					unset( $wp_rewrite->extra_rules_top[$rewrite_rule] );
					$rewrite_rule = $new_rule . substr( $rewrite_rule, strlen( $rules['content_types'][$rule['content_type']]['slug'] ) );

					$wp_rewrite->extra_rules_top[$rewrite_rule] = $rule_struct;					

				}
			}

			// permalinks
			$wp_rewrite->extra_permastructs[$rules['content_types'][$rule['content_type']]['slug']]['struct'] = '/' . $new_rule . substr( $wp_rewrite->extra_permastructs[$rules['content_types'][$rule['content_type']]['slug']]['struct'], ( strlen( $rules['content_types'][$rule['content_type']]['slug'] ) + 1 ) );

		}


	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CEW_PATH ) . '/languages');

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		
		if( false !== strpos( $screen->base, 'caldera_easy_rewrites' ) ){

			wp_enqueue_style( 'caldera_easy_rewrites-core-style', CEW_URL . '/assets/css/styles.css' );
			wp_enqueue_style( 'caldera_easy_rewrites-baldrick-modals', CEW_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'caldera_easy_rewrites-wp-baldrick', CEW_URL . '/assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'caldera_easy_rewrites-core-script', CEW_URL . '/assets/js/scripts.js', array( 'caldera_easy_rewrites-wp-baldrick' ) , false );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );			
		
		}


	}


}















