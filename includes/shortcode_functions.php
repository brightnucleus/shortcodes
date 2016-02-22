<?php
/**
 * Shortcode Functions.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

/**
 * Execute a specific shortcode directly from code.
 *
 * @since 0.2.4
 *
 * @param string      $tag     Tag of the shortcode to execute.
 * @param array       $atts    Array of attributes to pass to the shortcode.
 * @param string|null $content Inner content to pass to the shortcode.
 * @return string|false Rendered HTML.
 */
function do_tag( $tag, array $atts = [ ], $content = null ) {

	global $shortcode_tags;

	if ( ! array_key_exists( $tag, $shortcode_tags ) ) {
		return false;
	}

	return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}
