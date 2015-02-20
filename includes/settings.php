<?php
/**
 * Caldera URL Builder Setting.
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
class Settings_Caldera_URL_Builder extends Caldera_URL_Builder{


	/**
	 * Start up
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );
		// save config
		add_action( 'wp_ajax_cub_save_config', array( $this, 'save_config') );
		// rebuild rules
		add_action( 'wp_ajax_cub_rebuild_rules', array( $this, 'rebuild_rules') );
		// test rules
		add_action( 'wp_ajax_cub_test_rules', array( $this, 'test_rules') );


		
	}


	/**
	 * rebuilds rules
	 */
	public function rebuild_rules(){
		global $wp_rewrite;

		flush_rewrite_rules();

		$this->test_rules( true );

	}


	/**
	 * test rules without saving.
	 */
	public function test_rules( $rebuilt = false ){
		global $wp_rewrite;
		
		if( empty( $rebuilt ) ){
			$config = json_decode( stripslashes_deep( $_POST['config'] ), true );
			$config = $this->add_sanitization_and_validation( $config );
			$rules = $this->define_rewrites( $config );
			$wp_rewrite->matches = 'matches';
		}else{
			$rules = $this->define_rewrites( true );
		}

		$results = array();
		if( empty( $rules ) ){
			wp_send_json_success( $results );
		}

		// fetch rules - (build mode not cached)
		$rewrite = $wp_rewrite->rewrite_rules();

		foreach( $rules as $type=>$struct ){

			$posts = get_posts( array('post_type' => $type, 'posts_per_page' => 1 ) );
			if( empty( $posts ) ){
				$results[ $type ] = array( 'warning' => __( 'No posts to test.', 'caldera-url-builder' ) );
				continue;
			}

			if( !empty( $struct[0] ) && false !== strpos( $struct[0], '_root_warning_' ) && empty( $rules['page'] ) ){
				//$results[ $type ] = array( 'warning' => __( 'A variable as the first part will clash with pages, Create a rule for pages or set the first part as static.  ', 'caldera-url-builder' ) );
				$results[ $type ][] = array('pagename' => true );
				//continue;
			}

			$post = $posts[0];
			// get a permalink for the first post found.
			$url = $this->create_permalink( get_permalink( $post->ID ), $post->ID );

			// Strip 'index.php/' if we're not using path info permalinks
			if ( !$wp_rewrite->using_index_permalinks() ){
				$url = str_replace( $wp_rewrite->index . '/', '', $url );
			}

			// Chop off http://domain.com/[path]
			$url = str_replace(home_url(), '', $url);

			// Trim leading and lagging slashes
			$url = trim( $url, '/');

			$request = $url;
			$post_type_query_vars = array();

			foreach ( get_post_types( array() , 'objects' ) as $post_type => $t ) {
				if ( ! empty( $t->query_var ) )
					$post_type_query_vars[ $t->query_var ] = $post_type;
			}

			// Look for matches.
			$request_match = $request;
			foreach ( (array)$rewrite as $match => $query) {


				// If the requesting file is the anchor of the match, prepend it
				// to the path info.
				if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
					$request_match = $url . '/' . $request;

				if ( preg_match("#^$match#", $request_match, $matches) ) {

					if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
						// This is a verbose page match, let's check to be sure about it.
						if ( ! get_page_by_path( $matches[ $varmatch[1] ] ) )
							continue;
					}

					// Got a match.
					// Trim the query of everything up to the '?'.
					$query = preg_replace("!^.+\?!", '', $query);

					// Substitute the substring matches into the query.
					$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

					// Filter out non-public query vars
					global $wp;
					parse_str( $query, $query_vars );
					$query = array();
					foreach ( (array) $query_vars as $key => $value ) {
						if ( in_array( $key, $wp->public_query_vars ) ){
							$query[$key] = $value;
							if ( isset( $post_type_query_vars[$key] ) ) {
								$query['post_type'] = $post_type_query_vars[$key];
								$query['name'] = $value;
							}
						}
					}

					// Do the query
					//$query = new WP_Query( $query );
					$results[ $type ][] = $query;

				}
			}

		}

		wp_send_json_success( $results );
	}
	/**
	 * saves a config
	 */
	public function save_config(){

		if( empty( $_POST['caldera-url-builder-setup'] ) || !wp_verify_nonce( $_POST['caldera-url-builder-setup'], 'caldera-url-builder' ) ){
			if( empty( $_POST['config'] ) ){
				return;
			}
		}
		
		// reset rewrites

		if( !empty( $_POST['caldera-url-builder-setup'] ) && empty( $_POST['config'] ) ){
			$config = stripslashes_deep( $_POST );
			$config = $this->add_sanitization_and_validation( $config );
			Caldera_URL_Builder_Options::save( $config );
			flush_rewrite_rules();
			wp_redirect( '?page=caldera_easy_rewrites&updated=true' );
			exit;
		}

		if( !empty( $_POST['config'] ) ){
			$config = json_decode( stripslashes_deep( $_POST['config'] ), true );
			$config = $this->add_sanitization_and_validation( $config );
			if(	wp_verify_nonce( $config['caldera-url-builder-setup'], 'caldera-url-builder' ) ){
				Caldera_URL_Builder_Options::save( $config );
				$this->rebuild_rules();

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
				$filtered = Settings_Caldera_URL_Builder_Sanitize::apply_sanitization_and_validation( $setting, $value, $config );
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
			$this->plugin_screen_hook_suffix['caldera_url_builder'] =  add_submenu_page(
				'options-general.php',
				__( 'Caldera URL Builder', $this->plugin_slug ),
				__( 'URL Builder', $this->plugin_slug ),
				'manage_options',
				'caldera_url_builder'
				, array( $this, 'create_admin_page' )
			);

			add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix[ 'caldera_url_builder' ], array( $this, 'enqueue_admin_stylescripts' ) );


	}


	/**
	 * Options page callback
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		include CUB_PATH .'includes/edit.php';

		// php based script include
		if( file_exists( CUB_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
				include CUB_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}


}

if( is_admin() || defined( 'DOING_AJAX ') ){
	$settings_caldera_easy_rewrites = new Settings_Caldera_URL_Builder();
}
