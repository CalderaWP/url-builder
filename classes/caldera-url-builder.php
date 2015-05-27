<?php
/**
 * Caldera URL Builder Main Class
 *
 * @package   Caldera_URL_Builder
 * @author    CalderaWP <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 CalderaWP <david@digilab.co.za>
 */

/**
 * Plugin class.
 * @package Caldera_URL_Builder
 * @author  CalderaWP <david@digilab.co.za>
 */
class Caldera_URL_Builder {

	/**
	 * @var      string
	 */
	protected $plugin_slug = 'caldera-url-builder';
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
	protected $rule_structs = array();

	/**
	 * The saved rewrites
	 *
	 * @since 0.2.0
	 *
	 * @var array|bool
	 */
	protected $saved;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		// load text domain
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
		add_filter( 'post_type_archive_link', array( $this, 'create_archive_permalink' ), 10, 2 );
		add_filter( 'term_link', array( $this, 'create_permalink' ), 10, 2 );

		add_filter('rewrite_rules_array', array( $this, 'archive_rules_rewrite' ) );
		add_filter('rewrite_rules_test_array', array( $this, 'archive_rules_rewrite' ) );

		//get saved settings
		$this->saved = Caldera_URL_Builder_Options::get_all();
		
	}

	
	/**
	 * Return a new structured archive permalink.
	 *
	 * @param string	$post_link	current post url link
	 * @param int 		$post_id	current post ID
	 *
	 * @return    string    url permalink
	 */
	public function create_archive_permalink( $link, $post_type ){
		
		//$rules = $this->rule_structs[ $post->post_type ];
		return home_url() . '/' . $this->rule_structs[$post_type . '_archive'];

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
		if( is_object( $post_id ) && isset( $post_id->term_id ) ){
			return $post_link;
		}
		$post = get_post( $post_id );
		if( empty( $post ) || empty( $this->rule_structs[ $post->post_type ] ) ){
			return $post_link;
		}
		
		if( $post->post_type === 'post' ){
			$post_link = home_url( user_trailingslashit( $wp_rewrite->permalink_structure ) );
			if( false === $sample ){
				$post_link = str_replace( '%postname%', $post->post_name, $post_link );
			}
		}

		$rules = $this->rule_structs[ $post->post_type ];
		
		foreach( $rules as $node_id => $tax ){
			$taxonomy = $tax[ 'taxonomy' ];
			$terms = wp_get_post_terms( $post->ID, $taxonomy );
			if( !is_wp_error( $terms ) && !empty( $terms ) ){
				$value = $terms[0]->slug;
			}else{
				$value = $tax[ 'default' ];
			}
			
			$post_link = str_replace( '/%' . $node_id .'%/', '/' . $value . '/', $post_link );
		}

		return $post_link;

	}



	/**
	 * filter finilized rules to addin additionals like archives
	 *
	 *
	 * @return    array   rewrite rules array
	 */
	public function archive_rules_rewrite( $rewrites ){
		
		global $wp_rewrite;

		/// create archives
		// load up the rules
		if( empty( $test_config ) || true === $test_config ){
			$rules = $this->saved;
		}else{
			$rules = $test_config;
		}			

		$rule_list = array();
		$post_types = get_post_types( null, 'objects' );
		if( empty( $rules['rewrite'] ) ){
			return $rewrites;
		}
		// start working on em.
		foreach( $rules['rewrite'] as $rule_id=>$rule ){


			if( false === $rule['pass'] || false === strpos( $rule['content_type'], '_archive'  ) ){
			continue;
			}

			// structs
			$structure = array();

			if( !empty( $rule['segment'] ) ){
				foreach( $rule['segment'] as $segment ){

					$structure[] = $segment['path'];
				}
			}

			if( false === strpos( $rule['content_type'], 'taxonomy_'  ) ){

				$post_type = substr( $rule['content_type'], 0, strlen( $rule['content_type'] ) - 8 );
				$args = $post_types[ $post_type ];
				
				$original_archive_slug = $args->has_archive === true ? $args->rewrite['slug'] : $args->has_archive;

				$archive_slug = implode( '/', $structure );

				if ( $args->rewrite['with_front'] ){
					$archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
				}else{
					$archive_slug = $wp_rewrite->root . $archive_slug;
				}
				
				unset( $rewrites["{$original_archive_slug}/?$"] );
				$rule_list["{$archive_slug}/?$"] = "index.php?post_type=$post_type";

				add_rewrite_rule( "", "", 'top' );

				if ( $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
					$feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
					unset( $rewrites["{$original_archive_slug}/feed/$feeds/?$"] );
					unset( $rewrites["{$original_archive_slug}/$feeds/?$"] );

					$rule_list["{$archive_slug}/feed/$feeds/?$"] = "index.php?post_type=$post_type" . '&feed=$matches[1]';
					$rule_list["{$archive_slug}/$feeds/?$"] = "index.php?post_type=$post_type" . '&feed=$matches[1]';
				}

				if ( $args->rewrite['pages'] ){
					unset( $rewrites["{$original_archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$"] );
					$rule_list["{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$"] = "index.php?post_type=$post_type" . '&paged=$matches[1]';
				}

			}else{
				
				$taxonomy = str_replace( '_archive', '', str_replace( 'taxonomy_', '', $rule['content_type'] ) );
				$args = get_taxonomy( $taxonomy );
				$archive_slug = implode( '/', $structure );
				/*$args['rewrite'] = wp_parse_args( $args['rewrite'], array(
					'with_front' => true,
					'hierarchical' => false,
					'ep_mask' => EP_NONE,
				) );*/
				
				
				foreach( $rewrites as $rule_url=>$rewrite_rule ){
					if( substr( $rule_url, 0, strlen( $taxonomy ) ) == $taxonomy ){

						$rule_list[ $archive_slug . substr( $rule_url, strlen( $taxonomy ) ) ] = $rewrite_rule;
						
					}
				}

				$args->rewrite['slug'] = $archive_slug;

				if ( $args->hierarchical && $args->rewrite['hierarchical'] )
					$tag = '(.+?)';
				else
					$tag = '([^/]+)';

				$link_url = $args->query_var ? "{$args->query_var}=" . '$matches[1]' : "taxonomy=$taxonomy";
				
				$rule_list["{$archive_slug}/{$tag}/?$"] = 'index.php?' . $link_url;
				//add_rewrite_tag( "%$taxonomy%", $tag, $args['query_var'] ? "{$args['query_var']}=" : "taxonomy=$taxonomy&term=" );
				//add_permastruct( $taxonomy, "{$args['rewrite']['slug']}/%$taxonomy%", $args['rewrite'] );


				
				//add_rewrite_tag( "%$taxonomy%", $tag, $args['query_var'] ? "{$args['query_var']}=" : "taxonomy=$taxonomy&term=" );
				//add_permastruct( $taxonomy, "{$archive_slug}", $args->rewrite );

			}


		}

		$sync = array_reverse( $rewrites, true );
		$rule_list = array_reverse( $rule_list, true );

		$sync = array_merge( $sync, $rule_list );

		$rewrites = array_reverse( $sync, true );
		
		return $rewrites;
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
			$rules = $this->saved;
		}else{
			$rules = $test_config;
		}

		if( empty( $rules['rewrite'] ) ){
			if ( $this->rebuild_flag() ) {
				$this->rebuild_flag( false );
			}

			return;

		}

		$rule_list = array();
		// start working on em.
		foreach( $rules['rewrite'] as $rule_id=>$rule ){

			if( false === $rule['pass'] ){
				continue;
			}

			// structs
			$structure = array();
			$args = array();

			if( !empty( $rule['segment'] ) ){
				$first = true;
				foreach( $rule['segment'] as $segment_id=>$segment ){

					switch ( $segment['type'] ) {
						case 'taxonomy':

							if ( isset( $segment[ 'default' ] ) ) {
								$structure[] = '%' . $segment_id . '%';
								add_rewrite_tag( '%' . $segment_id . '%', '([^&^/]+)', $segment['taxonomy'] . '=' );


								$this->rule_structs[ $rule['content_type'] ][ $segment_id ] = array(
									'default'  => $segment['default'],
									'taxonomy' => $segment['taxonomy'],
								);


								if ( ( ! empty( $test_config ) || true === $test_config ) && $first = true ) {
									$rule_list[ $rule['content_type'] ][] = '_root_warning_' . $segment['default'];
								} else {
									$rule_list[ $rule['content_type'] ][] = $segment['default'];
								}
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
				// type
				if( false !== strpos( $rule['content_type'], '_archive') ){
					// ignored - passed to later filter
					$this->rule_structs[ $rule['content_type'] ] = implode( "/", $rule_list[$rule['content_type']] );

				}elseif( false !== strpos( $rule['content_type'], 'taxonomy_') ){
					// taxo
					$rule['content_type'] = str_replace( 'taxonomy_', '', $rule['content_type'] );
					$structure[] = '%' . $rule['content_type'] . '%';
					add_permastruct( $rule['content_type'], implode( '/', $structure ), $wp_rewrite->extra_permastructs[ $rule['content_type'] ] );

					//$this->rule_structs[ $rule['content_type'] ] = implode( "/", $rule_list[$rule['content_type']] );
				}else{
				
					$structure[] = '%' . $rule['content_type'] . '%';
					add_permastruct( $rule['content_type'], implode( '/', $structure ), $wp_rewrite->extra_permastructs[ $rule['content_type'] ] );
				}
	
			}

		}

		if( is_array( $test_config ) ){
			return $rule_list;
		}


		$this->rebuild_flag( false );

	}

	/**
	 * Get the value of the "rebuild needed" flag or attempt to flush if its set.
	 *
	 * @since 0.3.0
	 *
	 * @param bool $get Optional. If true, just value is returned, if false, the whole check if flush and maybe flush is performed.
	 *
	 * @return mixed|void
	 */
	protected function rebuild_flag( $get = true ) {
		$flag = get_option( '_cer_rebuild_rules', false );
		if ( $get ) {
			return $flag;

		}


		if( !empty( $flag ) ){
			global $wp_rewrite;
			delete_option( '_cer_rebuild_rules' );
			$wp_rewrite->flush_rules( false );
			wp_safe_redirect( $_SERVER['REQUEST_URI'] );
			exit;
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

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CUB_PATH ) . '/languages');

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		
		if( false !== strpos( $screen->base, 'caldera_url_builder' ) ){

			wp_enqueue_style( 'caldera_url_builder-core-style', CUB_URL . '/assets/css/styles.css' );
			wp_enqueue_style( 'caldera_url_builder-baldrick-modals', CUB_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'caldera_url_builder-wp-baldrick', CUB_URL . '/assets/js/wp-baldrick-full.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'caldera_url_builder-core-script', CUB_URL . '/assets/js/scripts.js', array( 'caldera_url_builder-wp-baldrick' ) , false );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );			
		
		}


	}


}








/**
 * Software Licensing
 */
add_action( 'admin_init', function( ) {
	$params = array(
		'store_url' => 'http://calderawp.com',
		'version'   => CUB_VER,
		'item_name' => 'Caldera URL Builder',
		'author'    => 'CalderaWP',
		'plugin_root_file' => CUB_PATH . 'plugincore.php',
		'prefix'    => 'cub'
	);

	$cub_licensing = new \calderawp\licensing\main( $params );

	$button_baldrick = array(
		'action' => 'cub_save_license',
		'before' => 'cub_license_before',
		'callback' => 'cub_license_cb'

	);

	/**
	 * @var calderawp\licensing\output
	 */
	global $cub_licensing_output;
	$cub_licensing_output = new calderawp\licensing\output( $cub_licensing, $button_baldrick );


}, 0 );






