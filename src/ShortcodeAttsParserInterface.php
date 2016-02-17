<?php
/**
 * Shortcode Attributes Parser Interface.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
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
	 * @param  array $atts Attributes passed to the shortcode.
	 * @return array       Validated attributes of the shortcode.
	 */
	public function parse_atts( $atts );
}
