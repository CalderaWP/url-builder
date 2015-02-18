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
	 * @var      array
	 */
	protected $rule_structs = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		/// setup rewrite rules
		add_action( 'init', array( $this, 'define_rewrites' ), 100 );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// filter permalinks.
		add_filter( 'post_type_link', array( $this, 'create_permalink' ), 10, 3 );
		add_filter( 'post_link', array( $this, 'create_permalink' ), 1, 3 );
		add_filter( 'attachment_link', array( $this, 'create_permalink' ), 10, 3 );
		
	}

	/**
	 * Return a new structured permalink.
	 *
	 * @param string	$post_link	current post url link
	 * @param int 		$post_id	current post ID
	 *
	 * @return    string    url permalink
	 */
	public function create_permalink( $post_link, $post_id, $sample = false){

		global $wp_rewrite;
		$post = get_post( $post_id );

		if( empty( $this->rule_structs[ $post->post_type ] ) ){
			return $post_link;
		}
		
		if( $post->post_type === 'post' ){
			$post_link = home_url( user_trailingslashit( $wp_rewrite->permalink_structure ) );
			if( false === $sample ){
				$post_link = str_replace( '%postname%', $post->post_name, $post_link );
			}
		}
		
		foreach( $this->rule_structs[ $post->post_type ] as $taxonomy=>$default ){
			$terms = wp_get_post_terms( $post->ID, $taxonomy );
			if( !is_wp_error( $terms ) && !empty( $terms ) ){
				$default = $terms[0]->slug;
			}
			
			$post_link = str_replace( '/%' . $taxonomy .'%/', '/' . $default . '/', $post_link );
		}

		return $post_link;

	}
	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public function define_rewrites( $test_config = false ){
		
		global $wp_rewrite;

		// get built_in names
		$built_in = get_post_types( array( '_builtin' => true ), 'names' );

		// load up the rules
		if( empty( $test_config ) || true === $test_config ){
			$rules = get_option( '_caldera_easy_rewrites' );
		}else{
			$rules = $test_config;
		}

		if( empty( $rules['rewrite'] ) ){
			return;
		}
		$rule_list = array();
		// start working on em.
		foreach( $rules['rewrite'] as $rule_id=>$rule ){
			// structs
			$structure = array();
			$args = array();

			if( !empty( $rule['segment'] ) ){
				$first = true;
				foreach( $rule['segment'] as $segment_id=>$segment ){

					switch ( $segment['type'] ) {
						case 'taxonomy':
							//$structure[] = '%' . $segment['taxonomy'] . '%';
							$structure[] = '%' . $segment_id . '%';
							add_rewrite_tag( '%' . $segment_id . '%', '([^&^/]+)', $segment['taxonomy'] . '=' );
							//$this->rule_structs[ $rule['content_type'] ][ $segment['taxonomy'] ] = $segment['default'];
							$this->rule_structs[ $rule['content_type'] ][ $segment_id ] = $segment['default'];
							
							if( ( !empty( $test_config ) || true === $test_config ) && $first = true ){
								$rule_list[ $rule['content_type'] ][] = '_root_warning_' . $segment['default'];
							}else{
								$rule_list[ $rule['content_type'] ][] = $segment['default'];
							}
							

							break;						
						case 'static':
							$structure[] = $segment['path'];
							$rule_list[ $rule['content_type'] ][] = $segment['path']; 
							break;
						default:
							# code...
							break;
					}

					$first = false;
				}
			}

			if( $rule['content_type'] === 'post' ){
	
				$structure[] = '%postname%';
				$wp_rewrite->permalink_structure = implode( '/', $structure );


			}elseif( $rule['content_type'] === 'page' ){
	
				$structure[] = '%pagename%';
				$wp_rewrite->page_structure = implode( '/', $structure );
	
			}else{
				
				$structure[] = '%' . $rule['content_type'] . '%';
				add_permastruct( $rule['content_type'], implode( '/', $structure ), $wp_rewrite->extra_permastructs[ $rule['content_type'] ] );
	
			}

		}

		if( false !== $test_config ){
			return $rule_list;
		}

	}

	/**
	 * Return an instance of this class.
	 *
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
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CEW_PATH ) . '/languages');

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
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















