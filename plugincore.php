<?php
/**
 * @package   Caldera_URL_Builder
 * @author    CalderaWP <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 CalderaWP <david@digilab.co.za>
 *
 * @wordpress-plugin
 * Plugin Name: Caldera URL Builder
 * Plugin URI:  http://CalderaWP.com/downlaods/caldera-url-builder
 * Description: Visual editor for WordPress permalinks.
 * Version:     0.1.0
 * Author:      David Cramer for CalderaWP <david@calderaWP.com>
 * Author URI:  http://calderawp.com/
 * Text Domain: caldera-url-builder
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CUB_PATH',  plugin_dir_path( __FILE__ ) );
define('CUB_URL',  plugin_dir_url( __FILE__ ) );
define('CUB_VER',  '0.1.0' );


// load internals
require_once( CUB_PATH . '/classes/caldera-url-builder.php' );
require_once( CUB_PATH . 'classes/magic-slugs.php' );
require_once( CUB_PATH . '/classes/options.php' );
require_once( CUB_PATH . 'includes/settings.php' );

// activation hook
register_activation_hook( __FILE__, 'flush_rewrite_rules');

// Load instance
add_action( 'plugins_loaded', array( 'Caldera_URL_Builder', 'get_instance' ) );


/**
 * Deactivation hook for this plugin.
 *
 * Flushes permalinks.
 *
 * @since 0.2.0
 */
register_deactivation_hook( __FILE__, 'caldera_url_builder_deactivate' );
function caldera_url_builder_deactivate() {
	global $wp_rewrites;
	flush_rewrite_rules();

}
