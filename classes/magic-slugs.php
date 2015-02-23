<?php
/**
 * Caldera URL Builder Magic Slugs
 *
 * Parses %post.something% or %category.
 *
 * @package   Caldera_URL_Builder
 * @author    CalderaWP <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 CalderaWP <david@digilab.co.za>
 */

/**
 * Class Caldera_URL_Builder_Magic_Slug
 *
 * @package Caldera_URL_Builder
 * @author  Josh Pollock <Josh@JoshPress.net>
 */
class Caldera_URL_Builder_Magic_Slug {

	/**
	 * Checks if a "magic" slug is valid
	 *
	 * @since 0.0.2
	 *
	 * @param string $segment The slug to check
	 *
	 * @return bool|string Either traversal or callback if valid. Else, false.
	 */
	public static function validate_magic_slug( $segment ) {
		switch ( $segment ) {
			case  0 === strpos( $segment, '%' ) && false === strpos( $segment, '@'  ) && self::is_allowed_traversal( self::strip_tags_on( $segment ) )   :
				return 'traversal';
				break;
			case  0 ===  strpos($segment, '@'  ) && false === strpos( $segment, '%' ) && function_exists( self::strip_tags_on( $segment ) )  :
				return 'callback';
				break;
			default :
				return false;
				break;
		}

	}

	/**
	 * Strip illegal characters from segments
	 *
	 * @since 0.2.0
	 *
	 * @param string $segment
	 *
	 * @return string
	 */
	protected static function strip_tags_on( $segment ) {
		$segment = str_replace( array( '%', '@'), '', $segment );

		return $segment;
	}

	/**
	 * Do the magic slug, if possible.
	 *
	 * @since 0.2.0
	 *
	 * @param string $segment Segment to possibly parse.
	 * @param int $segment_number The segment number.
	 * @param string $content_type Content type for rewrite being run.
	 * @param bool $single If is a single, not archive.
	 * @param object $post Post object.
	 *
	 * @return bool|mixed|null|void
	 */
	public static function maybe_do_magic_slug( $segment, $segment_number, $content_type, $single, $post ) {

		if ( 0 === strpos( $segment, '%' )  || 0 === strpos( $segment, '@' ) ) {
			$type = self::validate_magic_slug( $segment );
			if ( $type ) {
				switch( $type ) {
					case $type == 'traversal' :
						$segment = self::traversal( self::strip_tags_on( $segment ), $segment_number, $post );
						break;
					case $type == 'callback' :
						$segment = self::callback( self::strip_tags_on( $segment ), $segment_number, $content_type, $single );
						break;
					default :
						$segment = null;
						break;
				}

				if ( is_string( $segment ) ) {
					return $segment;

				}

			}else {
				return false;

			}
		}else{
			return $segment;

		}

	}

	/**
	 * Run the callback function
	 *
	 * @param string $segment Segment to possibly parse.
	 * @param int $segment_number The segment number.
	 * @param string $content_type Content type for rewrite beign run.
	 * @param bool $single If is a single, not archive.
	 *
	 * @return mixed
	 */
	protected static function callback( $segment, $segment_number, $content_type, $single ) {
		$segment_number = self::number_to_string( (int) $segment_number );
		$callback_func = $segment;
		if( $single ) {
			$segment = call_user_func( $callback_func, $segment_number, get_post_type_object( $content_type ) );
		}else{
			//not supported yet.
		}

		return $segment;

	}

	/**
	 * Do the post/category traversal.
	 *
	 * @since 0.2.0
	 *
	 * @param string $segment Segment to possibly parse.
	 * @param int $segment_number The segment number.
	 * @param object $post The $post object.
	 *
	 * @return bool|mixed|void
	 */
	protected static function traversal( $segment, $segment_number, $post ) {
		$parts = self::prepare_traversal( $segment );
		if ( ! is_object( $post ) ) {
			return;

		}

		$out = false;

		switch ( $parts ) {
			case $parts[0] === 'post' && isset( $post->$parts[1] ) :
				$out = $post->$parts[1];
				break;
			case $parts[0] === 'category' :
				return self::term( 'category', $post, $parts );
				break;

		}

		$traversal_type = $parts[0];

		$out = apply_filters( 'caldera_easy_rewrites_traversal', $out, $traversal_type, $segment_number );

		return $out;

	}

	/**
	 * Check if a prefix for a traversal is valid.
	 *
	 * @since 0.2.0
	 *
	 * @return array|mixed|void
	 */
	protected static function allowed_prefixes()  {
		$prefixes = array(
			'post',
			'category'
		);

		//@todo figue out how to make this work with self::traversal or disable
		$prefixes = apply_filters( 'caldera_easy_rewrites_magic_prefixes', $prefixes );

		if ( ! is_array( $prefixes ) ) {
			$prefixes = array();
		}

		return $prefixes;

	}

	/**
	 * Turn traversable string into an array.
	 *
	 * @since 0.2.0
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	protected static function prepare_traversal( $string ) {
		$string = str_replace( '%', '', $string );
		$traversal = explode( '.', $string );
		if ( isset( $traversal[0] ) && isset ( $traversal[1] ) ) {
			return array( $traversal[0], $traversal[1] );

		}

	}

	/**
	 * Check if traversal is allowed
	 *
	 * @since 0.2.0
	 *
	 * @param $string
	 *
	 * @return bool
	 */
	protected static function is_allowed_traversal( $string ) {
		$prepared = self::prepare_traversal( $string );
		if ( is_array( $prepared ) && ( in_array( $prepared[0], self::allowed_prefixes() ) ) ) {
			return true;

		}

	}

	/**
	 * Make an integer the word for said integer.
	 *
	 * @since 0.2.0
	 *
	 * Only compatible with 0-9
	 *
	 * @param int $int
	 *
	 * @return string
	 */
	protected static function number_to_string( $int ) {
		$map = array( 'zero', 'one','two','three', 'four', 'five', 'six', 'seven', 'eight', 'nine' );
		if ( isset( $map[ $int ] ) ) {
			return $map[ $int ];
			
		}

	}

	/**
	 * Get segment from term
	 *
	 * @since 0.2.0
	 *
	 * @param $post
	 * @param $parts
	 *
	 * @return bool
	 */
	protected static function term( $taxonomy, $post, $parts ) {
		$args  = array( 'orderby' => 'name', 'order' => 'ASC', 'fields' => 'slugs' );

		$args = apply_filters( 'caldera_easy_rewrites_term_lookup_args', $args, $taxonomy, $post, $parts );

		$terms = wp_get_post_terms( $post->ID, $taxonomy, $args );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			if ( isset( $terms[ $parts[1] ] ) ) {
				return $terms[1];
			} elseif ( $parts[1] === 0 || $parts[1] === '0' ) {
				return $terms[0];
			} else {
				end( $terms );
				$last = key( $terms );

				return $terms[ $last ];
			}

		}

	}


}
