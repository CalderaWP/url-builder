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
	}

	/**
	 * Return a new structured permalink.
	 *
	 * @param string	$post_link	current post url link
	 * @param int 		$post_id	current post ID
	 *
	 * @return    string    url permalink
	 */
	public function create_permalink( $post_link, $post_id, $sample ){

		global $wp_query;

		$post = get_post( $post_id );
		if( true === $sample ){
			$post->post_name = '%postname%'; // allow the edit
		}

		if ( is_wp_error($post) || !isset( $this->rule_structs[$post->post_type] ) || ( empty( $post->post_name ) && false === $sample ) ){
			return $post_link;
		}

		$new_link = array();

		foreach( $this->rule_structs[$post->post_type] as $path ){
			if( is_array( $path ) ){
				// is this a taxonomy?
				if( !empty( $path['taxonomy'] ) ){

					// determin if its in an archive page already.
					if( !empty( $wp_query->query[$path['taxonomy']] ) ){
						$new_link[] = $wp_query->query[$path['taxonomy']];
					}else{

						$terms = get_the_terms( $post->ID, $path['taxonomy'] );

						if( is_wp_error( $terms ) || empty( $terms ) ) {
							$new_link[] = $path['default'];
						}else {
							$term_obj = array_pop( $terms );
							$new_link[] = $term_obj->slug;
						}
					}

				}else{
					// need a handler
				}
			}else{
				$new_link[] = $path;
			}
		}

		// add name
		$new_link[] = $post->post_name;
		
		return home_url(user_trailingslashit( implode( '/', $new_link ) ) );
	}
	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public function define_rewrites(){

		// load up the rules
		$rules = get_option( '_caldera_easy_rewrites' );

		if( empty( $rules['rewrite'] ) ){
			return;
		}
		// start working on em.
		foreach( $rules['rewrite'] as $rule_id=>$rule ){		
			// structs
			$structure = array();
			$args = array();
			$link = array();

			$tags[] = $rule['slug'];
			//$structure[] = $rule['slug'];
			$args[] = 'post_type=' . $rule['content_type'];
			//$link[] = $rule['slug'];

			// archive rewrite tag
			//add_rewrite_tag('%' . $rule_id . '%', '(' . $rule['slug'] .')s', 'post_type=' );

			$index = 1;	
			if( !empty( $rule['segment'] ) ){
				foreach( $rule['segment'] as $segment_id=>$segment ){

					switch ( $segment['type'] ) {
						case 'taxonomy':
							add_rewrite_tag('%' . $segment_id . '%', '([^&]+)', $segment['taxonomy'] . '=' );
							$tags[] = $segment['taxonomy'];
							$structure[] = '([^/]+)';
							$args[] = $segment['taxonomy'] . '=$matches[' . $index++ . ']';
							$link[] = array( 'taxonomy' => $segment['taxonomy'], 'default' => $segment['default'] );
							break;
						
						case 'static':
							$tags[] = $link[] = $structure[] = $segment['path'];
							break;
						default:
							# code...
							break;
					}

				}
			}

			// record structure for link filter
			$this->rule_structs[$rule['content_type']] = $link;
			$args[] = $rule['content_type'] . '=$matches[' . $index . ']';

			// rewtire string and path
			$string 	= "^" . implode( '/', $structure ) . "/([^/]+)/?";
			$rewrite 	= 'index.php?' . implode( '&', $args );

			// post rewrite rule
			add_rewrite_rule( $string, $rewrite, 'top' );

			// archives rewrite rule.
			array_shift( $tags ); // get rid of the slug

			add_permastruct( $rule['content_type'], '/' . implode('/',  $tags ) . '/' );
			//add_permastruct( $rule['content_type'] . '_archive', implode('/',  $tags ) );
			//add_permastruct( $rule['content_type'] . '_archive', '%' . $rule_id . '%/' . implode('/',  $tags ) );
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















