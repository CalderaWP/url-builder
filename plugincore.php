<?php
/**
 * @package   Caldera_Easy_Rewrites
 * @author    CalderaWP <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 CalderaWP <david@digilab.co.za>
 *
 * @wordpress-plugin
 * Plugin Name: Caldera Easy Rewrites
 * Plugin URI:  
 * Description: Easily create custom rewrites for content types.
 * Version:     0.1.0
 * Author:      CalderaWP <david@digilab.co.za>
 * Author URI:  http://calderawp.com/
 * Text Domain: caldera-easy-rewrites
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CEW_PATH',  plugin_dir_path( __FILE__ ) );
define('CEW_URL',  plugin_dir_url( __FILE__ ) );
define('CEW_VER',  '0.1.0' );


// load internals
require_once( CEW_PATH . '/classes/caldera-easy-rewrites.php' );
require_once( CEW_PATH . 'classes/magic-slugs.php' );
require_once( CEW_PATH . '/classes/options.php' );
require_once( CEW_PATH . 'includes/settings.php' );

// activation hook
register_activation_hook( __FILE__, 'flush_rewrite_rules');

// Load instance
add_action( 'plugins_loaded', array( 'Caldera_Easy_Rewrites', 'get_instance' ) );
