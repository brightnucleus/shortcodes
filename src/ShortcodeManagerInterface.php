<?php
/**
 * Shortcode Manager Interface.
 *
 * @package   BrightNucleus\Shortcode
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2015-2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\Shortcode;

/**
 * Shortcode Manager Interface.
 *
 * This interface manages all the shortcodes that it gets passed within a
 * ConfigInterface.
 *
 * @since   0.2.4
 *
 * @package BrightNucleus\Shortcode
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ShortcodeManagerInterface {

	/**
	 * Register all of the shortcode handlers.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $context Optional. Context information to pass to shortcode.
	 * @return void
	 */
	public function register( $context = null );

	/**
	 * Register the shortcode UI handlers.
	 *
	 * @since 0.1.0
	 */
	public function register_shortcode_ui();

	/**
	 * Execute a specific shortcode directly from code.
	 *
	 * @since 0.2.4
	 *
	 * @param string      $tag     Tag of the shortcode to execute.
	 * @param array       $atts    Array of attributes to pass to the shortcode.
	 * @param string|null $content Inner content to pass to the shortcode.
	 * @return string Rendered HTML.
	 */
	public function do_tag( $tag, array $atts = [ ], $content = null );
}