<?php
/**
 * Shortcode Interface.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

use BrightNucleus\Contract\Registerable;

/**
 * Shortcode Interface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ShortcodeInterface extends Registerable {

	/**
	 * Render the shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param  array       $atts    Attributes to modify the standard behavior
	 *                              of the shortcode.
	 * @param  string|null $content Content between enclosing shortcodes.
	 * @return string               The shortcode's HTML output.
	 */
	public function render( $atts, $content = null );

	/**
	 * Get the shortcode tag.
	 *
	 * @since 0.1.0
	 *
	 * @return string Shortcode tag.
	 */
	public function get_tag();

	/**
	 * Execute this shortcode directly from code.
	 *
	 * @since 0.2.4
	 *
	 * @param array       $atts    Array of attributes to pass to the shortcode.
	 * @param string|null $content Inner content to pass to the shortcode.
	 * @return string|false Rendered HTML.
	 */
	public function do_this( array $atts = [ ], $content = null );
}
