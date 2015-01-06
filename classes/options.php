<?php
/**
 * Caldera Easy Rewrites Options.
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
class Caldera_Easy_Rewrites_Options {


	/**
	 * Get an option from this plugin.
	 *
	 * @param string $option The name of a specific option to get.
	 * @param mixed $default Optional. Default to return if no value found. Default is false.
	 *
	 * @return string|null|array Returns the option or null if it doesn't exist
	 */
	public static function get ( $option, $default = false ) {
		$option = self::get_options( $option );
		if ( is_array( $option ) && empty( $option ) ) {
			return null;

		}

		if ( is_null( $option ) ) {
			return $default;

		}

		return $option;

	}

	/**
	 * Get all option from this plugin.
	 *
	 * @return null|array Returns the options or null if none are set
	 */
	public static function get_all (  ) {
		return self::get_options( null );

	}

	/**
	 * Get an option or all option from this plugin
	 *
	 * @access private
	 *
	 * @param null|string $option Optional. If null, the default, all options for this plugin are returned. Provide the name of a specific option to get just that one.
	 *
	 * @return array|null|string
	 */
	private static function get_options( $option = null ) {
		$options = get_option( "_caldera_easy_rewrites", array() );
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

}
