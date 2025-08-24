<?php
/**
 * Bright Nucleus Shortcode Component.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

/**
 * Shortcode Attributes Parser Interface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ShortcodeAttsParserInterface {

	/**
	 * Register the shortcode handler function with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @param  array  $atts Attributes passed to the shortcode.
	 * @param string $tag  Tag of the shortcode.
	 * @return array       Validated attributes of the shortcode.
	 */
	public function parse_atts( $atts, $tag );
}
