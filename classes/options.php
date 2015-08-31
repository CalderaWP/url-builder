<?php
/**
 * Caldera URL Builder Options.
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
class Caldera_URL_Builder_Options {

	/**
	 * The name of the plugin's main options
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public static $option_name = '_caldera_url_builder';


	/**
	 * Get an option from this plugin.
	 *
	 * @since 0.0.1
	 *
	 * @param string $option The name of a specific option to get.
	 * @param mixed $default Optional. Default to return if no value found. Default is false.
	 *
	 * @return string|null|array|string Returns the option or null if it doesn't exist or false if not allowed.
	 */
	public static function get ( $option, $default = false ) {
		$can = self::can();
		if( $can ){
			$option = self::get_options( $option );
			if ( is_array( $option ) && empty( $option ) ) {
				return null;

			}

			if ( is_null( $option ) ) {
				return $default;

			}

			return $option;
		} else {

			return $can;
		}

	}

	/**
	 * Get all option from this plugin.
	 *
	 * @since 0.0.1
	 *
	 * @return null|array|bool Returns the options or null if none are set or false if not allowed.
	 */
	public static function get_all ( ) {
		$can = self::can();
		if( $can ){
			return self::get_options( null );

		} else {
			return $can;

		}

	}

	/**
	 * Get an option or all option from this plugin
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 *
	 * @param null|string $option Optional. If null, the default, all options for this plugin are returned. Provide the name of a specific option to get just that one.
	 *
	 * @return array|null|string
	 */
	private static function get_options( $option = null ) {
		$options = get_option( self::$option_name, array() );
		if ( empty( $options ) ) {
			return $options;

		}

		if ( ! is_null( $option ) ) {
			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}
			else {
				return null;

			}

		}

		return $options;

	}

	/**
	 * Clear this plugin's saved config.
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public static function clear() {
		$can = self::can();
		if( $can ){
			return delete_option( self::$option_name );

		}else{
			return $can;

		}

	}

	/**
	 * Save this plugin's options
	 *
	 * @since 0.2.0
	 *
	 * @param array $config The configuration to save.
	 *
	 * @return bool
	 */
	public static function save( $config ) {
		$can = self::can();
		if( $can ){
			return update_option( self::$option_name, $config );
		}else{
			return $can;

		}

	}

	/**
	 * Generic capability check to use before reading/writing
	 *
	 * @since 1.1.2
	 *
	 * @param string $cap Optional. Capability to check. Defaults to 'manage_options'
	 *
	 * @return bool
	 */
	public static function can( $cap = 'manage_options' ) {
		return current_user_can( $cap );

	}

}
